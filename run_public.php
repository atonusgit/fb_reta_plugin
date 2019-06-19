<?php
require_once __DIR__ . '/../vendor/autoload.php';
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;

/**
 *	Tokens
 */
$app_id = '<place_holder>';
$app_secret = '<place_holder>';
$access_token = '<place_holder>';
$ad_account_id = '<place_holder>';
$pixel_id = '<place_holder>';

/**
 *	Presets
 */
$campaign_id = '<place_holder>';
$custom_audience_id = '<place_holder>';
$adset_id = '<place_holder>';
$ad_creative_id = '<place_holder>';

/**
 *	Variables
 */
$picture_url = '<place_holder>';
$url = '<place_holder>';
$message = '<place_holder>';
$slug = '<place_holder>';
$end_time = '<place_holder>';
$sl_business_name = '<place_holder>';
$sl_logo = '<place_holder>';
$page_id = '<place_holder>';

/**
 *	Initialization
 */
$api = Api::init($app_id, $app_secret, $access_token);

/**
 *	Create campaign
 */
	$fields = array();
	$params = array(
		'name'									=> $slug . ' - Campaign',
		'objective'								=> 'LINK_CLICKS',
		'status'								=> 'PAUSED',
	);
	$campaign_id = (new AdAccount($ad_account_id))->createCampaign(
		$fields,
		$params
	)->exportAllData()['id'];
	echo 'campaign id: ' . $campaign_id . "\n\r";

/**
 *	Create custom audience
 */
	$fields = array();
	$params = array(
		'name'									=> $slug . ' - Custom Audience',
		'rule'									=> array(
			'inclusions'						=> array(
				'operator'						=> 'or',
				'rules'							=> array(
					array(
						'event_sources'			=> array(
							array(
								'id'			=> $pixel_id,
								'type'			=> 'pixel'
							)
						),
						'retention_seconds'		=> 2 * 86400 * 31, // 2 months
						'filter'				=> array(
							'operator'			=> 'and',
							'filters'			=> array(
								array(
									'field'		=> 'url',
									'operator'	=> 'i_contains',
									'value'		=> $slug
								)
							)
						)
					)
				)
			)
		),
		'prefill'								=> '1',
	);
	$custom_audience_id = (new AdAccount($ad_account_id))->createCustomAudience(
		$fields,
		$params
	)->exportAllData()['id'];
	echo 'custom audience id: ' . $custom_audience_id . "\n\r";

/**
 *	Create adset
 */
	$fields = array();
	$params = array(
		'name' 									=> $slug . ' - AdSet',
		'optimization_goal' 					=> 'REACH',
		'billing_event' 						=> 'IMPRESSIONS',
		'bid_amount' 							=> '2',
		'lifetime_budget'						=> '3000',
		'campaign_id' 							=> $campaign_id,
		'targeting' 							=> array(
			'custom_audiences' 					=> array(
				'id' 							=> $custom_audience_id
			)
		),
		'end_time'								=> $end_time,
	);
	$adset_id = (new AdAccount($ad_account_id))->createAdSet(
		$fields,
		$params
	)->exportAllData()['id'];
	echo 'adset id: ' . $adset_id . "\n\r";

/**
 *	Create ad creative
 */
	$fields = array();
	$params = array(
		'name'									=> $slug . ' - Creative',
		'object_story_spec'						=> array(
			'page_id'							=> $page_id,
			'link_data'							=> array(
				'picture'						=> $picture_url,
				'link'							=> $url,
				'message'						=> $message
			)
		),
		'sponsorship_info_spec'					=> array(
			'sponsor_name'						=> $sl_business_name,
			'sponsor_image_url'					=> $sl_logo
		)
	);
	$ad_creative_id = (new AdAccount($ad_account_id))->createAdCreative(
		$fields,
		$params
	)->exportAllData()['id'];
	echo 'ad_creative id: ' . $ad_creative_id . "\n\r";

/**
 *	Create ad
 */
	$fields = array();
	$params = array(
		'name'									=> $slug . ' - Ad',
		'adset_id'								=> $adset_id,
		'creative'								=> array(
			'creative_id'						=> $ad_creative_id
		),
		'status'								=> 'PAUSED',
	);
	$ad_id = (new AdAccount($ad_account_id))->createAd(
		$fields,
		$params
	)->exportAllData()['id'];
	echo 'ad_id: ' . $ad_id . "\n\r";