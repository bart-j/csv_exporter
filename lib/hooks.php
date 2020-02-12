<?php
/**
 * All plugin hook callbacks are bundled here
 */

/**
 * Extend the default exportable values with some extra's
 * 
 * @param string $hook        'get_exportable_values'
 * @param string $type        'csv_exporter'
 * @param array  $returnvalue the current exportable values
 * @param array  $params      supplied params
 * 
 * @return array
 */
function csv_exporter_get_exportable_values_hook($hook, $type, $returnvalue, $params) {
	
	if (!empty($params) && is_array($params)) {
		$type = elgg_extract("type", $params);
		$subtype = elgg_extract("subtype", $params);
		$readable = (bool) elgg_extract("readable", $params, false);
		
		if ($readable) {
			// defaults
			$returnvalue = array_merge($returnvalue, array(
				elgg_echo("csv_exporter:exportable_value:owner_name") => "csv_exporter_owner_name",
				elgg_echo("csv_exporter:exportable_value:owner_username") => "csv_exporter_owner_username",
				elgg_echo("csv_exporter:exportable_value:owner_email") => "csv_exporter_owner_email",
				elgg_echo("csv_exporter:exportable_value:owner_url") => "csv_exporter_owner_url",
				elgg_echo("csv_exporter:exportable_value:container_name") => "csv_exporter_container_name",
				elgg_echo("csv_exporter:exportable_value:container_username") => "csv_exporter_container_username",
				elgg_echo("csv_exporter:exportable_value:container_email") => "csv_exporter_container_email",
				elgg_echo("csv_exporter:exportable_value:container_url") => "csv_exporter_container_url",
				elgg_echo("csv_exporter:exportable_value:time_created_readable") => "csv_exporter_time_created_readable",
				elgg_echo("csv_exporter:exportable_value:time_updated_readable") => "csv_exporter_time_updated_readable",
				elgg_echo("csv_exporter:exportable_value:url") => "csv_exporter_url",
			));
				
			switch ($type) {
				case "object":
					$returnvalue[elgg_echo("tags")] = "csv_exporter_tags";

					switch ($subtype) {
						case "poll":
							$returnvalue[elgg_echo("csv_exporter:poll_results")] = "csv_exporter_poll_results";
							break;
					}

					break;
				case "user":
					// add profile fields
					$profile_fields = elgg_get_config("profile_fields");
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$lan = $metadata_name;
							if (elgg_echo("profile:" . $metadata_name) != $metadata_name) {
								$lan = elgg_echo("profile:" . $metadata_name);
							}
							$returnvalue[$lan] = $metadata_name;
						}
					}
					
					// others
					$returnvalue[elgg_echo("email")] = "email";
					$returnvalue[elgg_echo("tags")] = "csv_exporter_tags";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:user:site_newsletter")] = "csv_exporter_site_newsletter";

					$returnvalue[elgg_echo("csv_exporter:exportable_value:user:last_action")] = "csv_exporter_user_last_action";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:user:last_action_readable")] = "csv_exporter_user_last_action_readable";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:user:last_login")] = "csv_exporter_user_last_login";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:user:last_login_readable")] = "csv_exporter_user_last_login_readable";
						
					break;
				case "group":
					// add profile fields
					$profile_fields = elgg_get_config("group");
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$lan = $metadata_name;
							if (elgg_echo("groups:" . $metadata_name) != $metadata_name) {
								$lan = elgg_echo("groups:" . $metadata_name);
							}
							$returnvalue[$lan] = $metadata_name;
						}
					}
						
					// others
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:member_count")] = "csv_exporter_group_member_count";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:admins")] = "csv_exporter_group_admins";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:admins:email")] = "csv_exporter_group_admins_email";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:last_activity")] = "csv_exporter_group_last_activity";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:last_activity_readable")] = "csv_exporter_group_last_activity_readable";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:total_file_size")] = "csv_exporter_group_total_file_size";
					break;
			}
		} else {
			// defaults
			$returnvalue = array_merge($returnvalue, array(
				"csv_exporter_owner_name",
				"csv_exporter_owner_username",
				"csv_exporter_owner_email",
				"csv_exporter_owner_url",
				"csv_exporter_container_name",
				"csv_exporter_container_username",
				"csv_exporter_container_email",
				"csv_exporter_container_url",
				"csv_exporter_time_created_readable",
				"csv_exporter_time_updated_readable",
				"csv_exporter_url",
			));
			
			switch ($type) {
				case "object":
					$returnvalue[] = "csv_exporter_tags";
					break;
				case "user":
					// add profile fields
					$profile_fields = elgg_get_config("profile_fields");
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$returnvalue[] = $metadata_name;
						}
					}
					
					//others
					$returnvalue[] = "email";
					$returnvalue[] = "csv_exporter_tags";
					
					break;
				case "group":
					// add profile fields
					$profile_fields = elgg_get_config("group");
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$returnvalue[] = $metadata_name;
						}
					}
					
					// others
					$returnvalue[] = "csv_exporter_group_member_count";
					$returnvalue[] = "csv_exporter_group_last_activity";
					$returnvalue[] = "csv_exporter_group_last_activity_readable";
					$returnvalue[] = "csv_exporter_group_total_file_size";
					break;
			}
		}
	}
	
	return $returnvalue;
}

/**
 * Return a value to be exported, return null to allow default behaviour
 * 
 * @param string $hook        'export_value'
 * @param string $type        'csv_exporter'
 * @param mixed  $returnvalue the current value
 * @param array  $params      supplied params
 * 
 * @return null|string
 */
function csv_exporter_export_value_hook($hook, $type, $returnvalue, $params) {
	
	if (empty($returnvalue) && !empty($params) && is_array($params)) {
		$type = elgg_extract("type", $params);
		$entity = elgg_extract("entity", $params);
		$exportable_value = elgg_extract("exportable_value", $params);
		
		switch ($exportable_value) {
			case "csv_exporter_owner_name":
				$owner = $entity->getOwnerEntity();
				if ($owner) {
					if ($owner->name) {
						$returnvalue = $owner->name;
					} else {
						// the owner is an ElggObject
						$returnvalue = $owner->title;
					}
				}
				break;
			case "csv_exporter_owner_username":
				$owner = $entity->getOwnerEntity();
				if ($owner) {
					if (elgg_instanceof($owner, "user")) {
						$returnvalue = $owner->username;
					} else {
						// the owner is not an ElggUser
						$returnvalue = $owner->getGUID();
					}
				}
				break;
			case "csv_exporter_owner_email":
				$owner = $entity->getOwnerEntity();
				if ($owner) {
					if (elgg_instanceof($owner, "user")) {
						$returnvalue = $owner->email;
					}
				}
				break;
			case "csv_exporter_owner_url":
				$owner = $entity->getOwnerEntity();
				if ($owner) {
					if (!elgg_instanceof($owner, "site")) {
						$returnvalue = $owner->getURL();
					} else {
						// the owner is an ElggSite
						$returnvalue = $owner->url;
					}
				}
				break;
			case "csv_exporter_container_name":
				$container = $entity->getContainerEntity();
				if ($container) {
					if ($container->name) {
						$returnvalue = $container->name;
					} else {
						// the container is an ElggObject
						$returnvalue = $container->title;
					}
				}
				break;
			case "csv_exporter_container_username":
				$container = $entity->getContainerEntity();
				if ($container) {
					if (elgg_instanceof($container, "user")) {
						$returnvalue = $container->username;
					} else {
						// the container is not an ElggUser
						$returnvalue = $container->getGUID();
					}
				}
				break;
			case "csv_exporter_container_email":
				$container = $entity->getContainerEntity();
				if ($container) {
					if (elgg_instanceof($container, "user")) {
						$returnvalue = $container->email;
					}
				}
				break;
			case "csv_exporter_container_url":
				$container = $entity->getContainerEntity();
				if ($container) {
					if (!elgg_instanceof($container, "site")) {
						$returnvalue = $container->getURL();
					} else {
						// the container is an ElggSite
						$returnvalue = $container->url;
					}
				}
				break;
			case "csv_exporter_time_created_readable":
				$returnvalue = date(elgg_echo("friendlytime:date_format"), $entity->time_created);
				break;
			case "csv_exporter_time_updated_readable":
				$returnvalue = date(elgg_echo("friendlytime:date_format"), $entity->time_updated);
				break;
			case "csv_exporter_url":
				if (!elgg_instanceof($entity, "site")) {
					$returnvalue = $entity->getURL();
				} else {
					// the entity is an ElggSite
					$entity->url;
				}
				break;
			case "csv_exporter_tags":
				if ($entity->tags) {
					$tags = $entity->tags;
					if (!is_array($tags)) {
						$tags = array($tags);
					}
					
					$returnvalue = implode(", ", $tags);
				}
				break;
			case "csv_exporter_site_newsletter":
				$site = elgg_get_site_entity();
				$returnvalue = check_entity_relationship($entity->guid, "subscribed", $site->guid) ? "yes" : "no";
				break;
			case "csv_exporter_user_last_action":
				$returnvalue = $entity->last_action;
				break;
			case "csv_exporter_user_last_action_readable":
				$returnvalue = date(elgg_echo("friendlytime:date_format"), $entity->last_action);
				break;
			case "csv_exporter_user_last_login":
				$returnvalue = $entity->last_login;
				break;
			case "csv_exporter_user_last_login_readable":
				$returnvalue = date(elgg_echo("friendlytime:date_format"), $entity->last_login);
				break;
			case "csv_exporter_group_member_count":
				if (elgg_instanceof($entity, "group")) {
					$returnvalue = $entity->getMembers(0, 0, true);
				}
				break;
			case "csv_exporter_group_admins":
				if (elgg_instanceof($entity, "group")) {
					if (function_exists('group_tools_get_admins')) {
						$admins = group_tools_get_admins($entity);
						$list = array_map(function($a) { return $a->name; }, $admins);
						$returnvalue = implode(", ", $list);
					} else {
						$returnvalue = "";
					}
				}
				break;
			case "csv_exporter_group_admins_email":
				if (elgg_instanceof($entity, "group")) {
					if (function_exists('group_tools_get_admins')) {
						$admins = group_tools_get_admins($entity);
						$list = array_map(function($a) { return $a->email; }, $admins);
						$returnvalue = implode(", ", $list);
					} else {
						$returnvalue = "";
					}
				}
				break;
			case "csv_exporter_group_last_activity":
				if (elgg_instanceof($entity, "group")) {
					$returnvalue = csv_exporter_get_last_group_activity($entity);
				}
				break;
			case "csv_exporter_group_last_activity_readable":
				if (elgg_instanceof($entity, "group")) {
					$ts = csv_exporter_get_last_group_activity($entity);
					$returnvalue = date(elgg_echo("friendlytime:date_format"), $ts);
				}
				break;
			case "csv_exporter_group_total_file_size":
				if (elgg_instanceof($entity, "group")) {
					$dbprefix = elgg_get_config("dbprefix");

					$options = array(
						'type' => 'object',
						'subtype' => 'file',
						'container_guid' => (int) $entity->guid
					);
					$files = elgg_get_entities($options);

					$total_size = 0;
					foreach ($files as $file) {
						$filesize = $file->size();
						if ($filesize) {
							$total_size += $filesize;
						}
					}
					$returnvalue = $total_size;
				}
				break;

				
			case "csv_exporter_poll_results":
				$subtype = $entity->getSubtype();
				if ($subtype === "poll") {
					$results = [];
					foreach ($entity->getAnnotations("vote", 0, 0) as $annotation) {
						if (isset($results[$annotation->value])) {
							$results[$annotation->value] += 1;
						} else {
							$results[$annotation->value] = 1;
						}
					}

					$return = [];
					foreach ($results as $value => $count) {
						$return[] = "{$value} ({$count})";
					}


					return implode(", ", $return);
				}
				break;
			default:
				// check for profile fields
				if (($type == "user") || ($type == "group")) {
					if (is_array($entity->$exportable_value)) {
						$returnvalue = implode(", ", $entity->$exportable_value);
					}
				}
				
				break;
		}
	}
	
	return $returnvalue;
}
