<?php
/**
 * All helper functions for this plugin are bundled here
 */

/**
 * Get a list of all the exportable values for the given type/subtype
 *
 * @param string $type     the entity type
 * @param string $subtype  the entity subtype
 * @param bool   $readable readable values or just for processing (default: false)
 *
 * @return array
 */
function csv_exporter_get_exportable_values($type, $subtype = "", $readable = false) {
	$result = array();
	
	if (empty($type)) {
		return $result;
	}
	
	if (($type == "object") && empty($subtype)) {
		return $result;
	}
	
	$class = get_subtype_class($type, $subtype);
	if (!empty($class)) {
		$dummy = new $class();
	} else {
		switch ($type) {
			case "object":
				$dummy = new ElggObject();
				break;
			case "group":
				$dummy = new ElggGroup();
				break;
			case "site":
				$dummy = new ElggSite();
				break;
			case "user":
				$dummy = new ElggUser();
				break;
		}
	}
	
	$defaults = $dummy->getExportableValues();
	
	if ($readable) {
		$new_defaults = array();
		foreach ($defaults as $name) {
			if ($name != elgg_echo($name)) {
				$lan = elgg_echo($name);
			} else {
				$lan = elgg_echo("csv_exporter:exportable_value:" . $name);
			}
			$new_defaults[$lan] = $name;
		}
		
		$defaults = $new_defaults;
	}
	
	$params = array(
		"type" => $type,
		"subtype" => $subtype,
		"readable" => $readable,
		"defaults" => $defaults
	);
	$result = elgg_trigger_plugin_hook("get_exportable_values", "csv_exporter", $params, $defaults);
	
	if (is_array($result)) {
		// prevent duplications
		$result = array_unique($result);
	}
	
	return $result;
}

/**
 * Get the latest activity of this group based on the river
 *
 * @param ElggGroup $entity the group to check
 *
 * @return int the UNIX timestamp of the latest activity
 */
function csv_exporter_get_last_group_activity(ElggGroup $entity) {
	$result = 0;
	
	if (!empty($entity) && elgg_instanceof($entity, "group")) {
		$dbprefix = elgg_get_config("dbprefix");
		
		$query = "SELECT max(r.posted) as posted";
		$query .= " FROM " . $dbprefix . "river r";
		$query .= " INNER JOIN " . $dbprefix . "entities e ON r.object_guid = e.guid";
		$query .= " WHERE (e.container_guid = " . $entity->getGUID() . ")";
		$query .= " OR (r.object_guid = " . $entity->getGUID() . ")";
		
		$data = get_data($query);
		if ($data) {
			$result = (int) $data[0]->posted;
		}
	}
	
	return $result;
}
