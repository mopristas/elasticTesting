<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 26. 4. 2016
 * Time: 19:05
 */
class ElasticWrapper
{

	/** @var \Elasticsearch\Client */
	private $client;

	/**
	 * ElasticWrapper constructor.
	 */
	public function __construct()
	{
		$this->client = Elasticsearch\ClientBuilder::create()->build();
	}

	public function createStructure() {
		//Add index
		$params = [
			'index' => 'points'
		];
		$this->client->indices()->delete($params);
		$this->client->indices()->create($params);

		// Create the index mapping
		$params = [
			'index' => 'points',
			'type' => 'points_type',
			'body' => [
				'points_type' => [
					'_source' => [
						'enabled' => true
					],
					'properties' => [
						'location' => [
							'type' => 'geo_point'
						],
						'id' => [
							'type' => 'integer'
						]
					]
				]
			]
		];
		$this->client->indices()->putMapping($params);

	}

	public function addData() {
		$params = ['body' => []];
		$count = 0;
		for($i = 0; $i < 1000; $i++) {
			for ($j = 0; $j < 1000; $j++) {
				$params['body'][] = [
					'index' => [
						'_index' => 'points',
						'_type' => 'points_type',
						'_id' => $count
					]
				];

				$params['body'][] = [
					'location' => [
						'lat' => -5 + ($i / 100),
						'lon' => -5 + ($j / 100)
					],
				];

				$count++;
				if ($count % 2000 == 0) {
					$responses = $this->client->bulk($params);
//					print_r($responses);

					// erase the old bulk request
					$params = ['body' => []];

					// unset the bulk response when you are done to save memory
					unset($responses);
				}
			}
		}
	}

	public function loadData() {
		$location = [
			'lat' => 0.1,
			'lon' => -0.1
		];
		$params = [
			'index' => 'points',
			'type' => 'points_type',
			'body' => [
				'query' => [
					'match_all' => []
				],
				'filter' => [
					'geo_distance' => [
						'distance' => '10km',
						'location' => $location
					]
				]
			]
		];
		return $this->client->search($params);
	}

	public function deleteData()
	{
		$params = [
			'index' => 'points',
			'type' => 'points_type',
			'body' => [
				'query' => [
					'match_all' => []
				]
			]
		];
		return $this->client->deleteByQuery($params);

//		$params = ['body' => []];
//		$this->client->bulk();
//		for ($i = 1; $i <= 1000000; $i++) {
//			$params['body'][] = array(
//				'delete' => array(
//					'_index' => 'er',
//					'_type' => 'state',
//					'_id' => $i
//				)
//			);
//			if ($i % 2000 == 0) {
//				$this->client->bulk($params);
//				$params = ['body' => []];
//			}
//		}
	}

}