<?php
/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Orders {

	/**
	 * @var MatchaCUP PatientsOrders
	 */
	private $o;

	/**
	 * @var MatchaCUP PatientsOrderResults
	 */
	private $r;

	/**
	 * @var MatchaCUP PatientsOrderObservation
	 */
	private $b;

	/**
	 * Set Model App.model.patient.PatientsOrders
	 */
	private function setOrders(){
		if(!isset($this->o))
			$this->o = MatchaModel::setSenchaModel('App.model.patient.PatientsOrders');
	}

	/**
	 * Set Model App.model.patient.PatientsOrderResults
	 */
	private function setResults(){
		if(!isset($this->r))
			$this->r = MatchaModel::setSenchaModel('App.model.patient.PatientsOrderResult');
	}

	/**
	 * Set Model App.model.patient.PatientsOrderObservation
	 */
	private function setObservations(){
		if(!isset($this->b))
			$this->b = MatchaModel::setSenchaModel('App.model.patient.PatientsOrderObservation');
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getPatientOrders($params){
		$this->setOrders();
		return $this->o->load($params)->all();
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function addPatientOrder($params){
		$this->setOrders();
		return $this->o->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function updatePatientOrder($params){
		$this->setOrders();
		return $this->o->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function deletePatientOrder($params){
		$this->setOrders();
		return $this->o->destroy($params);
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getOrderResults($params){
		$this->setResults();
		return $this->r->load($params)->all();
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function addOrderResults($params){
		$this->setResults();
		return $this->r->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function updateOrderResults($params){
		$this->setResults();
		return $this->r->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function deleteOrderResults($params){
		$this->setResults();
		return $this->r->destroy($params);
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getOrderResultObservations($params){
		$this->setObservations();
		if(isset($params->loinc)){
			$records = $this->getObservationsByLoinc($params->loinc);
		}else{
			$records = $this->b->load($params)->all();
		}

		return $records;
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function addOrderResultObservations($params){
		$this->setObservations();
		return $this->b->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function updateOrderResultObservations($params){
		$this->setObservations();
		return $this->b->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function deleteOrderResultObservations($params){
		$this->setObservations();
		return $this->b->destroy($params);
	}

	private function getObservationsByLoinc($loinc, $primary = true){
		$this->setObservations();

		$records = $this->b->sql("SELECT DISTINCT p.LOINC_NUM AS code,
				                                  IF(e.ALIAS IS NULL OR e.ALIAS = '', l.shortname, e.ALIAS) AS code_text,
				                                  'LN' AS code_type,
				                                  IF(e.DEFAULT_UNIT IS NOT NULL, e.DEFAULT_UNIT, l.example_ucum_units) AS units,
				                                  CONCAT(e.RANGE_START, ' - ', e.RANGE_END) AS reference_rage,
				                                  e.HAS_CHILDREN AS has_children,
				                                  e.active
				                             FROM loinc_panels AS p
				                        LEFT JOIN loinc AS l ON p.LOINC_NUM = l.LOINC_NUM
				                        LEFT JOIN loinc_extra AS e ON e.LOINC_NUM = l.LOINC_NUM
				                            WHERE p.PARENT_LOINC != p.LOINC_NUM
   											  AND (e.active = '1' OR e.HAS_CHILDREN)
											  AND p.PARENT_LOINC = '$loinc'
				                         ORDER BY p.SEQUENCE")->all();

		foreach($records AS &$record){
			$record['id'] = null;
			$children = [];
			if($record['has_children']){
				// remove the parent from the record array and get the children
				unset($records[array_search($record, $records)]);
				$children = $this->getObservationsByLoinc($record['code'], false);
			}

			if(!empty($children)){
				$records = array_merge($records, $children);
			}
		}

		/**
		 * if primary recursion and records is empty (no panel)
		 * return the loinc info
		 */
		if($primary && empty($records)){

			$records = $this->b->sql("SELECT DISTINCT l.LOINC_NUM AS code,
		                                  	 IF(e.ALIAS IS NULL OR e.ALIAS = '', l.long_common_name, e.ALIAS) AS code_text,
		                                  	 'LN' AS code_type,
		                                  	 IF(e.DEFAULT_UNIT IS NOT NULL, e.DEFAULT_UNIT, l.example_ucum_units) AS units,
		                                  	 CONCAT(e.RANGE_START, ' - ', e.RANGE_END) AS reference_rage,
		                                  	 e.HAS_CHILDREN AS has_children,
		                                  	 e.active
			                            FROM loinc AS l
			                       LEFT JOIN loinc_extra AS e ON e.LOINC_NUM = l.LOINC_NUM
			                           WHERE e.active = '1'
								         AND l.loinc_num = '$loinc'")->all();
		}
		return $records;
	}


	public function getOrderResultObservationsByPidAndCode($pid, $code){
		$this->setOrders();
		$this->setResults();
		$this->setObservations();
		$sql = "SELECT obs.*, res.result_date
				  FROM patient_order_results_observations AS obs
			 LEFT JOIN patient_order_results AS res ON obs.result_id = res.id
			 LEFT JOIN patient_orders AS ord ON ord.id = res.order_id
				 WHERE obs.code = '$code'
				   AND ord.pid = '$pid'
			  ORDER BY res.result_date DESC
				 LIMIT 10";
		$records = $this->o->sql($sql)->all();
		return $records;
	}

	/**
	 * @param $pid
	 * @return mixed
	 */
	public function getOrderWithoutResultsByPid($pid){
		$this->setOrders();
		$this->setResults();
		$this->setObservations();

		$this->o->addFilter('pid', $pid);
		$orders = $this->o->load()->all();

		foreach($orders as $i => &$order){
			$this->r->addFilter('order_id', $order['id']);
			$result = $this->r->load()->one();

			if($result !== false){
				// if result delete order
				unset($orders[$i]);
				continue;
			}
		}
		unset($order);
		return $orders;
	}

	/**
	 * @param $pid
	 * @return mixed
	 */
	public function getOrderWithResultsByPid($pid){
		$this->setOrders();
		$this->setResults();
		$this->setObservations();

		$this->o->addFilter('pid', $pid);
		$orders = $this->o->load()->all();

		foreach($orders as $i => &$order){
			$this->r->addFilter('order_id', $order['id']);
			$result = $this->r->load()->one();

			if($result === false){
				// if no result delete order
				unset($orders[$i]);
				continue;
			}

			$order['result'] = &$result;
			$this->b->addFilter('result_id', $result['id']);
			$order['result']['observations'] = $this->b->load()->all();
			unset($result);
		}
		unset($order);
		return $orders;
	}

}
