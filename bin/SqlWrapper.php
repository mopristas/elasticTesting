<?php

/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 26. 4. 2016
 * Time: 19:06
 */
class SqlWrapper
{
	/** @var PDO */
	private $connection;
	/**
	 * ElasticWrapper constructor.
	 */
	public function __construct()
	{
		$dbName = 'test';
		$host = 'localhost';
		$username = 'root';
		$password = '';
		$dsn = "mysql:dbname=$dbName;host=$host";
		$this->connection = new PDO($dsn, $username, $password);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	}


	public function addData() {
		$sql = 'INSERT INTO points (location, id) VALUES ';
		$params = [];
		$count = 0;
		for($i = 0; $i < 1000; $i++) {
			for ($j = 0; $j < 1000; $j++) {
				$params[] = [
					'lat' => -5 + ($i / 100),
					'lon' => -5 + ($j / 100),
					'id' => $count
				];

				$count++;
				if ($count % 2000 == 0) {
					foreach ($params as $param) {
						$sql .= "(GeomFromText('POINT({$param['lat']} {$param['lon']})', 0), {$param['id']}), ";
					}
					$params = [];
					$sql = rtrim($sql, ", ");
					$sql .= ';';
//					file_put_contents('insert.sql', $sql);
					$stmt = $this->connection->prepare($sql);
					$stmt->execute();
					$sql = 'INSERT INTO points (location, id) VALUES ';
				}
			}
		}
	}

	public function loadData() {
		$location = [
			'lat' => 0.1,
			'lon' => -0.1
		];
		$stmt = $this->connection->prepare("		
		SELECT
		  x(location) AS lat,
		  y(location) AS lon
		FROM points
		WHERE (
			6371 * acos(
				cos(radians(0.1)) * cos(radians(x(location))) * cos(radians(y(location)) - radians(-0.1))
				+
				sin(radians(0.1)) * sin(radians(x(location)))
			)
		) < 10;
		");
		$stmt->execute();
		$stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function deleteData()
	{
		$stmt = $this->connection->prepare('TRUNCATE TABLE points');
		$stmt->execute();
	}
}