<?

//*****************  ВСТУПЛЕНИЕ   **************
// для капеллы - калькулятор с минимальным дизайном
if(strpos($_SERVER["SCRIPT_FILENAME"], 'show_page.php')){ 
    // инициализация
	require_once($_SERVER['DOCUMENT_ROOT'].'/include/init.php');

    ?>
    <script type="text/javascript" src="/templates/js/Jquery/jquery.min.js"></script>
    <link href="/templates/default/content.css" rel="stylesheet" type="text/css" />
    <link href="/templates/default/design.css" rel="stylesheet" type="text/css" />
    <link href="http://china-bel.by/panel/touchcd.css" rel="stylesheet" type="text/css" />
    <?
}





if(strpos($_SERVER["SCRIPT_FILENAME"], 'show_page.php')){ 
	$OBL = new obligascii_page(REG::I()->PARAM, REG::I()->url.'?');
} else {
	$OBL = new obligascii_page(REG::I()->PARAM, '/'.REG::I()->url.'?');
}

$OBL->getPage();
return 1;



class obligascii_page{

	protected $UserID;
	protected $action = '';
	protected $tplDir = '';
	protected $tplAbsDir = '';

	// ****  ГЛОБАЛЬНЫЕ ПЕРЕМЕННЫЕ МОДУЛЯ
	private $tableRefinansirovanie = 'calc.refinansirovanie';  // Cтавоки рефинансирования nbrb
	private $tableVkladyList = 'calc.dcalc_vklad_list';        // Список вкладов
	private $tableVkladUsloviya = 'calc.dcalc_vklad_usloviya'; // Списком условий по вкладам
	private $tableVkladValList = 'calc.dcalc_vklad_valuts';    // Список валют по вкладам
	private $tableVkladKapital = 'calc.dcalk_vklad_kapit';     // Список видов капитализации

	private $SR;  // Размер ставки рефинансирования на текущий день

	private $srok2text = array(
		'd'=> array( 'дней','день','дня'),  // 0, 1, 2
		'm'=> array( 'месяцев','месяц','месяца'), 
		'y'=> array( 'лет','год','года'),
	);


	public function __construct($PARAM, $action){
		session_start();

		$this->action = $action;
		$this->userGrantsLevel  =  PROTECT::allow($this->grantName);
		$this->userName = PROTECT::userName();
		$this->userID = PROTECT::userID();

		$this->tplDir = dirname(__FILE__).'/';
		$this->tplAbsDir = F::absDIR($this->tplDir);

		// Автоматически подгружаем темплейт по имени фйла модуля
		$tpl = preg_replace('~\.php$~','.htm',basename(__FILE__));
		TPL::LoadTplFile($tpl,'module', $this->tplDir, $this->tplAbsDir);


		// Получаем ставку рефинансирования на текущий день
		$this->SR = $this->getRefinansirovanie();
		$this->podohod = 13;
			
	}


	public function getPage(){

		if ($this->getParam('ajax')) {
			for ($i = 0 ; $i < 10 ; $i++) ob_end_clean();
			header('Content-type: text/html; charset=windows-1251');

			switch ($this->getParam('ajax')){
				case 'calculate':
					$this->ajax_calculate( $this->getParam('vklad') );
					break;
			}

			exit;
		}


		$DATA = array(
			'action'=>$this->action,
			'VKLAD_json' => custom_json::encode( $this->getVklady() ),
			'SR' => $this->getRefinansirovanie(),
		);


		//  подготавливаем данные о вкладах
		$VAL = $this->getValutsList();
		$Vklady = array();
		foreach ($VAL as $val_key=>$val_name){
			if($VKL = $this->getVklady( array('vklad_val' => $val_key) ) ){
				$Vklady[$val_key] = $VKL;
				$DATA['VAL'][$val_key] = $val_name;
			}
		}



		TPL::PrnTpl('obligacii_standart',$DATA);
		//F::debug($DATA);
	}



	private function getValutsList(){
		try{
			$T = DB::select_array("SELECT * FROM $this->tableVkladValList ORDER BY priority ASC ;");
			for ($i = 0 ; $i < count($T) ; $i++) {
				$VAL[$T[$i]['val_key']]=$T[$i]['val_name'];
			}
			return $VAL;
		}catch(Exception $e){
			$this->exception($e);
		}
	}



	private function getVklady($filter)
	{
		$where = ' visible  ';
		if($filter['vklad_val'])$where .= " AND vklad_val LIKE '$filter[vklad_val]' ";
		$VKL_id = DB::select_column("SELECT vklad_id FROM $this->tableVkladyList WHERE $where ORDER BY vklad_name;");
		
		$VKLADS = array();
		foreach ($VKL_id as $vklad_id) {
			$vklad = $this->getVklad($vklad_id);
			// форматируем некоторые поля
			$vklad['vklad_srok']  = $vklad['vklad_srok'].' '.F::NumberEnd($vklad['vklad_srok'],$this->srok2text[$vklad['vklad_srok_type']][0], $this->srok2text[$vklad['vklad_srok_type']][1], $this->srok2text[$vklad['vklad_srok_type']][2]);
			//$vklad['vklad_info']  = '<h3>Информация о вкладе</h3>'.$vklad['vklad_info'];
			//$vklad['vklad_info_procent']  = '<h3>Информация о выплате процентов</h3>'.$vklad['vklad_info_procent'];

			$vklad['vklad_procent'] +=0;  // Чтоб удалилась дробная часть
			
			if ($vklad['vklad_srok_do']!='') $vklad['vklad_srok_do'] = 'до '.$vklad['vklad_srok_do'];
			
			if($vklad['vklad_procent_plus_sr'] ){
				$vklad['vklad_procent'] = ' СР + '.$vklad['vklad_procent'].'% = ' . ($this->SR + $vklad['vklad_procent']).'%';
			}else{
				$vklad['vklad_procent'] .= ' %';
			}
			$VKLADS[$vklad['vklad_id']] = $vklad;
		}
		/*
		echo '<pre>';
		print_r ($VKLADS);
		echo '<pre>';
		*/
		return $VKLADS;
	}


	private function getVklad($VkladId)	{
		$VKLAD = DB::select_line("SELECT * FROM $this->tableVkladyList WHERE vklad_id = ?", $VkladId);
		if($USL = DB::select_array("SELECT * FROM $this->tableVkladUsloviya WHERE vklad_id = ? ORDER BY priority DESC", $VkladId)){
			$VKLAD["USL"] = $USL;
		}
		
		return $VKLAD;
	}


	private function getRefinansirovanie($date){
		try{
			if(!$date) $date = date('Ymd');
			$sr = DB::select_value("SELECT `value` as sr FROM $this->tableRefinansirovanie WHERE start_date <= ? AND end_date >= ?", $date,$date);
			if(DB::_error()) throw new Exception("Ошибка SQL при получении ставки рефинансирования.");
			return $sr+0;
		}catch (Exception $e){
			$this->exception($e);
		}
	}



	private function ajax_calculate($CALC)
	{	set_time_limit(5);
	try {




		// удаляем все лишние пробелы и меняем запятые на точки (чтобы не было ошибок) 
		$CALC['summa'] = preg_replace('~ +~','',$CALC['summa']);
		$CALC['summa'] = preg_replace('~,+~','.',$CALC['summa']);
				
		$vklad = $this->getVklad($CALC['vklad_id']);
		
		// определяем, нужно ли учитывать при рассчёте подоходный налог
		$CALC['podohod'] = 0;
		switch ($vklad["vklad_val"]) {
			case 'BYN':
				if($vklad["vklad_srok_type"] == 'm' AND $vklad["vklad_srok"] < 12) 		$CALC['podohod'] = 1;
				if($vklad["vklad_srok_type"] == 'd' AND $vklad["vklad_srok"] < 360) 	$CALC['podohod'] = 1;
			break;
			
			default:
				if($vklad["vklad_srok_type"] == 'y' AND $vklad["vklad_srok"] < 2) 		$CALC['podohod'] = 1;
				if($vklad["vklad_srok_type"] == 'm' AND $vklad["vklad_srok"] < 24) 		$CALC['podohod'] = 1;
				if($vklad["vklad_srok_type"] == 'd' AND $vklad["vklad_srok"] < 720) 	$CALC['podohod'] = 1;
			break;
		}


		if( !$CALC['val_key'])throw new Exception("Выберите валюту");
		if( !is_numeric($CALC['vklad_id']) || !$CALC['vklad_id'] )throw new Exception("Выберите вклад");
		if( !is_numeric($CALC['summa']) )throw new Exception("Cумма вклада должна быть числом");
		if( !is_numeric($CALC['summa']) || !$CALC['summa'] )throw new Exception("Ведите сумму вклада");
		
		if( ( $CALC[vklad_id] == 53 || $CALC[vklad_id] == 54 || $CALC[vklad_id] == 55 ) && (!$CALC['ref_nachalo'] || !$CALC['ref_konec'])) throw new Exception("Введите предполагаемые значения курсов НБ РБ");
		if (($CALC[vklad_id] == 53 || $CALC[vklad_id] == 54 || $CALC[vklad_id] == 55 ) && !preg_match('/^\+?\d+$/', $CALC['ref_nachalo'])) throw new Exception("Введите курс НБ РБ на дату открытия вклада");
		if (($CALC[vklad_id] == 53 || $CALC[vklad_id] == 54 || $CALC[vklad_id] == 55 ) && !preg_match('/^\+?\d+$/', $CALC['ref_konec'])) throw new Exception("Введите курс НБ РБ на дату закрытия вклада");
		if( $CALC['summa'] < $vklad['vklad_minimal'] )throw new Exception("Минимальная сумма вклада ".$vklad['vklad_minimal'].' '.$vklad['vklad_val']);

			
		//F::debug($vklad);

//*******************************
		// Устанавливаем стартовый период
		$PERIOD = array();
		$startDate = date('Ymd');
		//if (F::testers()) $startDate = '20140210';
		$endDate  = $this->DateDelta($startDate,$vklad['vklad_srok'].$vklad['vklad_srok_type']);
		if ($vklad['vklad_srok_do']!='') $endDate = substr($vklad['vklad_srok_do'],6,4).substr($vklad['vklad_srok_do'],3,2).substr($vklad['vklad_srok_do'],0,2);

		$PERIOD[ $startDate ] = array('summa'=>$CALC['summa']);
		$PERIOD[ $endDate  ] = array('kapital'=>'end');

		//******************************************
		// Если используется ставка рефинансирования
		if(1 || $vklad['vklad_procent_plus_sr']){

			//Устанавливаем ставку РФ для первого периода
			$ref1 = $this->getRefinansirovanie($startDate);
			$PERIOD[$startDate]['sr'] = $ref1;

			// В случае изменения ставки рефинансирования добавляем периоды
			$R = DB::select_array("SELECT * FROM $this->tableRefinansirovanie WHERE start_date >= ? AND start_date < ?",$startDate,$endDate);
			$REF = array();
			foreach ($R as $r) {
				$REF[$r['start_date']] = $r;
				$PERIOD[$r['start_date']]['sr'] = $r['value']+0; // не делать присвоение массивом (будет ошибка если начало вклада совпадет с днем смены ставки рефинансирования)
			}
		}

		//***********************************************************************************
		//Добавляем в периоды начало нового года (если учитывается реальное число дней в году)
		if($vklad['vklad_real_year_days']){
			$start_year = substr($startDate,0,4); 
			$end_year = substr($endDate,0,4);

			for($year=$start_year+1;$year<=$end_year;$year++) {
				// если периоды не високоcные, то и незачем добавлять лишний период для расчета
				//echo $year.'<br>';
				$PERIOD[$year.'0101'] = array();
			}
		}

			
		//********************************
		// Проставляем даты капитализаций
		$newKapDate = $startDate; $ind=1;
		// день, от которого весь отсчет периодов
		$DayOtschet = substr($startDate,6,2);
		while($vklad['vklad_kapital']>1 && $newKapDate<= $endDate){
			switch ( $vklad['vklad_kapital'] ){
				case 1: //По окончании срока вклада
					$newKapDate = $endDate;
					break;
				case 2: // Ежемесячно (на первое число каждого месяца)
					$newKapDate = $this->DateDelta($newKapDate , '+1m');
					$newKapDate = substr($newKapDate,0,6).'01';
					break;
				case 3: // Ежеквартально
					$newKapDate = $this->DateDelta($newKapDate , '+3m');
					break;
				case 4: // Каждые полгода
					$newKapDate = $this->DateDelta($newKapDate , '+6m');
					break;
				case 5: // Ежегодно
					$newKapDate = $this->DateDelta($newKapDate , '+1y');
					break;
				case 6: // Ежемесячно (по истечении месяца хранения)
					$newKapDate = $this->DateDelta($newKapDate , '+1m',$DayOtschet);
					break;
				case 7: // Ежемесячно (по истечении месяца хранения)
					$newKapDate = $this->DateDelta($newKapDate , '+1m',$DayOtschet);
					break;
			}

			//А вдруг перескок ?
			if($newKapDate<= $endDate) $PERIOD[$newKapDate] = array('kapital'=>$ind);
			$ind++;
		}


		// если по разным периодам разные процентные ставки - берет 5 периодов (c запасом)
		$i = 0;


		while($i<5){
			if (isset($vklad[USL][$i][srok_from]) && $vklad[USL][$i][srok_from]!=0) {
				$tempNewPeriodValue = '+'.$vklad[USL][$i][srok_from].$vklad[USL][$i][srok_type];
				$dateOtherProcent = $this->DateDelta($startDate , $tempNewPeriodValue);
				if(!$PERIOD[$dateOtherProcent]) {
					$PERIOD[$dateOtherProcent] = array('proc'=>$vklad[USL][$i][procent]);
				} else {
					$PERIOD[$dateOtherProcent]['proc'] = $vklad[USL][$i][procent];
					$PERIOD[$dateOtherProcent]['srok_from'] = $vklad[USL][$i][srok_from];
					$PERIOD[$dateOtherProcent]['srok_type'] = $vklad[USL][$i][srok_type];
					$PERIOD[$dateOtherProcent]['priority'] = $vklad[USL][$i][priority];
					$PERIOD[$dateOtherProcent]['procent_kvartal'] = $vklad[USL][$i][procent_kvartal];
				}
			}
		$i++;
		}

		//**************************
		// сортируем периоды по дате
		ksort($PERIOD);

		// для  первого периода подставляем ставку процентов по доп.капитализации


		//**********************************************************************
		// перебираем все периоды и расставляем количество дней в периоде и году
		// а также размер ставки рефинансирования
		$ind=1; $sr=0;
		foreach($PERIOD as $date=>$v){
			// Устанавливаем количество дней в году
			if($vklad['vklad_real_year_days']){
				$year = substr($date,0,4);
				$PERIOD[$date]['yeardays'] = ($year%4==0)?366:365;  // проверка года на высокосность
			}else{ $PERIOD[$date]['yeardays'] = 360; }

			// Установка начальной и конечной даты периода в днях c начала эпохи
			$PERIOD[$date]['start_date'] = $this->date2days( $date, $PERIOD[$date]['yeardays'] );
			$PERIOD[$date]['sd'] = $date;
			if( $prevdate ){
				$PERIOD[$prevdate]['ed'] = $date-1;
				$PERIOD[$prevdate]['end_date'] = $this->date2days($date,$PERIOD[$date]['yeardays'])-1;
			}

			/*if( $date==$endDate ){
				//$PERIOD[$prevdate]['ed'] = "$date";
				$PERIOD[$prevdate]['end_date'] = $this->date2days($date, $PERIOD[$date]['yeardays'])-1;
			}*/

			if($PERIOD[$date]['sr']) $sr = $PERIOD[$date]['sr'];
			$PERIOD[$date]['sr'] = $sr;

			$prevdate = $date;
			$ind++;
		}		

		//		if (F::testers()) F::debug($PERIOD);

		//*********************************
		//Удаляем последний ТЕМПОВЫЙ период
		//unset($PERIOD[$endDate]);

		//*********************************
		// Вычисляем длину каждого периода
		foreach($PERIOD as $date=>$v){		
			$PERIOD[$date]['length_days'] = $PERIOD[$date]['end_date']-$PERIOD[$date]['start_date']+1;
		}
		//if (F::testers()) F::debug($PERIOD); exit();
		//*********************************************************
		// Считаем ОСНОВНЫЕ ПРОЦЕНТЫ для каждого периода и суммарный
		$S=0;	// Сумма вклада
		$T=0;	// Проценты
		//$Tud=0;
		$priznak = 0;
		$ST = ''; $STsum = $CALC['summa'].' ';  //строка цифр для вывода
		$dopDohodKapitFormula = '';
		$dopDohodKapitProcent = '';
		$dopDohodKapitValue = 0;
		$Premial = 0;  // Cумма премиальных процентов
		$STPremial = '';  // Cумма премиальных процентов
		$PROCPremial = $vklad['vklad_dop_dohod']+0;  // процентная ставка премиальных процентов
		foreach($PERIOD as $date=>$v){

			if(!$PERIOD[$date]['summa']) $PERIOD[$date]['summa'] = $S;

			$per = &$PERIOD[$date];

			// Капитализация
			if($per['kapital'] && $CALC['podohod']!=1){
				$PERIOD[$date]['summa'] = $this->round( $PERIOD[$date]['summa'] + $Tsum , $vklad['vklad_val']);
				$PERIOD[$date]['STsum'] = $STsum . ' = <b><span class="nobr">' . $this->format_result($PERIOD[$date]['summa'],$vklad['vklad_val']).' '.$vklad['vklad_val'].'</span></b>';
				$STsum = $PERIOD[$date]['summa'] . ' ';
				$Tsum=0;
			}
			// Капитализация
			if($per['kapital'] && $CALC['podohod']==1){
				$PERIOD[$date]['summa'] = $this->round( $PERIOD[$date]['summa'] + $Tsum, $vklad['vklad_val']);
				$PERIOD[$date]['STsum'] = $STsum . ' = <b><span class="nobr">' . $this->format_result($PERIOD[$date]['summa'],$vklad['vklad_val']).' '.$vklad['vklad_val'].'</span></b>';
				$STsum = $PERIOD[$date]['summa'] -$Tpod . ' ';
				$STsum = ($this->round($STsum,$vklad['vklad_val']));
				$Tsum=0;
				$Tpod=0;
			}
			$S=explode("+", $STsum); $S=$S[0];
			$S =($this->round($S,$vklad['vklad_val']));
			// не считаем проценты после окончания срока вклада
			if($date >= $endDate) continue(1);
			
			// Вычисляем процент по условиям
			$per['proc'] = $vklad['vklad_procent'] + ( $vklad['vklad_procent_plus_sr'] ? $per['sr'] : 0 );
			$find = false;
			foreach( $vklad['USL'] as  $usl){
				//	summ_from,summ_to,srok_from,srok_to,srok_type,procent,procent_plus_sr
					
				if( ( $usl['summ_from']>0 && ( $per['summa'] > $usl['summ_from'] )) || 
					( $usl['srok_from']>0 && ( $date >= $this->DateDelta($startDate,$usl['srok_from'].$usl['srok_type']) ))
				){
						$find = true;
						$per['proc'] = $usl['procent'] + ( $usl['procent_plus_sr'] ? $per['sr'] : 0 );
						$per['USL'] =  '---'.$usl['summ_from'].'='.$per['summa'].'---';
						//echo $usl['procent_plus_sr'].'-'.$per['sr'].'-'.$usl['summ_from'].' < '.$per['summa'] .' < '.$usl['summ_to'].'  proc='.$per['proc'].'<br>';
						break;
				}
					
					
			}


			$P = $per['proc'];
			if ($CALC['podohod']!=1) $S = $per['summa'];
			$L = $per['length_days'];
			$d = $per['yeardays'];
			$T = ( $S * $P * $L ) / ($d * 100);

			$TP = $T*13/100;


			// для вкладов с доп.капитализацией
			/*
			if ($vklad['vklad_dopKapit_procent'] != 0) {
				if ($dopDohodKapitProcent == 0) {
					$dopDohodKapitProcent = $vklad['vklad_dopKapit_procent'];
					$dopDohodKapitFormula = "( $S * $dopDohodKapitProcent * $L ) / ($d * 100)";
					$dopDohodKapitValue =  ($S * $dopDohodKapitProcent * $L) / ($d * 100);
				} elseif(isset($per['procent_kvartal'])) {
					$dopDohodKapitProcent = $per['procent_kvartal'];
					$per['ST_dopKapit_dohod'] = $dopDohodKapitFormula."( $S * $dopDohodKapitProcent * $L ) / ($d * 100)";
					$dopDohodKapitFormula = '';

					$per['ST_dopDohodkapitValue'] =  (ceil($dopDohodKapitValue/100))*100;
					$T = $T + $per['ST_dopDohodkapitValue'];
					$dopDohodKapitValue =  ($S * $dopDohodKapitProcent * $L) / ($d * 100);
				} else {
					if($dopDohodKapitFormula == '') 	$dopDohodKapitFormula .= "( $S * $dopDohodKapitProcent * $L ) / ($d * 100)";
					else 								$dopDohodKapitFormula .= " + ( $S * $dopDohodKapitProcent * $L ) / ($d * 100)";
					$dopDohodKapitValue =  $dopDohodKapitValue + ($S * $dopDohodKapitProcent * $L) / ($d * 100);
				}
				//echo $dopDohodKapitValue.'<br />';

			}
			*/

			$per['ST'] = $ST = "( $S * $P * $L ) / ($d * 100) = $T";
			$per['ST_without_result'] = $ST = "( $S * $P * $L ) / ($d * 100)";


			$STsum .= (($STsum)? '+':''). " ( $S * $P * $L ) / ($d * 100)";
			if($PROCPremial >0){ 
				$Premial += ( $S * $PROCPremial * $L ) / ($d * 100);
				$STPremial .= (($STPremial)?' + ':'')."( $S * $PROCPremial * $L ) / ($d * 100)";
			}
						
			
			$per['T'] = $T;
			$Tsum +=$T;
			$per['Tsum'] = $Tsum;

			$per['TP'] = $TP;
			$Tpod +=$TP;
			$per['Tpod'] = $Tpod;

			//$Tud +=$T; 
			//$per['Tud'] = $Tud;
			//$PERIOD[$date]['dohod'] = $per['stoimost']*$per['proc']*$per['length_days'] / ( $per['yeardays']*100 );
			$dohod += $PERIOD[$date]['dohod'];
			$days += $per['length_days'];
		}

		// для вкладов с доп.капитализацией
		/*
		if ($vklad['vklad_dopKapit_procent'] != 0) {
			$keysMass = array_keys($PERIOD);
			$lastKeyMass = end($keysMass);
			$PERIOD[$lastKeyMass]['ST_dopKapit_dohod'] = $dopDohodKapitFormula;
			$PERIOD[$lastKeyMass]['ST_dopDohodkapitValue'] = (ceil($dopDohodKapitValue/100))*100;
			$T = $T + $per['ST_dopDohodkapitValue'];
			$PERIOD[$lastKeyMass]['T'] = $T;
			$Tsum +=$T;
			$PERIOD[$lastKeyMass]['Tsum'] = $Tsum;
		}

		if (F::testers()) F::debug($PERIOD);
		*/
		// 	В конце срока вклада ВСЕГДА капитализация
		if ($vklad['vklad_kapital']==1) {
			$PERIOD[$date]['kapital']=1;
			$PERIOD[$date]['summa'] = $this->round( $PERIOD[$date]['summa'] + $Tsum , $vklad['vklad_val']);
			$PERIOD[$date]['STsum'] = $STsum . ' = ' . $this->round($Tsum,$vklad['vklad_val']);

		}	
		
		$SUMM = $PERIOD[$date]['summa']; // Итоговая сумма вклада
		$PROC = $SUMM - $CALC['summa'];   // Итого процентов
		//***************************
		// Вывод результата расчетов
		$ttempVar = 0;
		$out_raschet = '';  $kapInd = 1;
		$i=0; 
		foreach($PERIOD as $date=>$v){	
			if(!$PERIOD[$date]['summa']) $PERIOD[$date]['summa'] = $S;

			$per = &$PERIOD[$date];
			if($vklad[vklad_kapital] == 1){
			if ($ttempVar == 0) {
					$out_raschet .= $CALC['summa'].' + ';
					$ttempVar = 1;
				}

				if ($i == count($PERIOD)-1) {
					$out_raschet = substr($out_raschet, 0, strlen($out_raschet)-2);
					$out_raschet .= ' = '.'<strong>'.$this->format_result($SUMM + $Premial + $dopolnitelnyDohod, $vklad['vklad_val']).' '.$vklad['vklad_val'].'</strong>';
					 $this->podohod = 13;$period = prev($PERIOD);
					$podohodn =  $PERIOD[$date]['summa'] - ($period['T'] * $this->podohod /100);
						$out_raschet .= ' ,<br>остаток вклада за вычетом подоходного налога '.$PERIOD[$date]['summa'].' - '.$this->format_result($period['T']).'*'.$this->podohod.' / 100 = <strong>'.$this->format_result($podohodn).' '.$vklad['vklad_val'].'</strong>';
				} else {
					$out_raschet .= $PERIOD[$date]['ST_without_result'].' + ' ;
					}
				$i++;
				
			} elseif($per['kapital']) {
				$PERIOD[$date]['summa'];
				if (isset($CALC['podohod'])==1) {

					if ($kapInd==1) {
						$a=prev($PERIOD);$a =$a['summa'];
					} else{$a=$podohodn;
						//$PERIOD[$date]['summa']=$b;
					}

					$out_raschet .= $kapInd .'-я капитализация: '. $PERIOD[$date]['STsum'].',<br>';

					$podohodn =  $PERIOD[$date]['summa'] - ($PERIOD[$date]['summa'] - $a)*$this->podohod /100;

					$out_raschet .= ' остаток вклада за вычетом подоходного налога '.$this->format_result($PERIOD[$date]['summa'],$vklad['vklad_val']).' - ('.$this->format_result($PERIOD[$date]['summa'],$vklad['vklad_val']).' - '.$this->format_result($a,$vklad['vklad_val']).') *'.$this->podohod.' / 100 = '.$this->format_result($podohodn).' '.$vklad['vklad_val'].'<br>';
				}else{
				$out_raschet .= $kapInd .'-я капитализация: '. $PERIOD[$date]['STsum'].'<br>';
				}
				/*
				if(isset($PERIOD[$date]['ST_dopKapit_dohod'])) 
					$out_raschet .= '<strong>Премиальные проценты: </strong>'.$PERIOD[$date]['ST_dopKapit_dohod'].' = <b>'.$this->format_result($PERIOD[$date]['ST_dopDohodkapitValue'], $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b><br>';
				*/
				//.' <span class="nobr"> остаток по вкладу: <b>'.$this->format_result($PERIOD[$date]['summa'], $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b></span><br>';
				$kapInd++;

			}
		}
		if($Premial>0) $out_raschet .= '<br><b>Расчет премиальных процентов </b><br>'.$STPremial.' = <b>'.$this->format_result($Premial, $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b>';
//		if($PREMIAL>0) $out_raschet .= '<br><b>Расчет премиальных процентов(вар2)</b><br>'.$PREMIAL_rachet.' = '.$PREMIAL.' '.$vklad['vklad_val'];
		
		if(0){
			ob_start();
			/*
			echo '<pre>';
			print_r($PERIOD);
			echo '</pre>'; 
			*/
			$out_raschet .= ob_get_clean(); 
		} 

		if ($vklad[vklad_name]=='Гарант (3 месяца)' || $vklad[vklad_name]=='Гарант (6 месяцев)' || $vklad[vklad_name]=='Гарант (12 месяцев)'){
			//F::debug($vklad);
			if ($vklad[vklad_srok_type] == 'm') { $srokInDays = $vklad[vklad_srok] * 30; }
			$dopolnitelnyDohodStavka = (($CALC['ref_konec'] / $CALC['ref_nachalo']) * 100 - 100 ) * ( 360 / $srokInDays);
			
			if ($CALC['ref_konec'] <= $CALC['ref_nachalo']) {
				$dopolnitelnyDohodStavkaFloor = 'без дополнительных процентов';
			} else {
				$dopolnitelnyDohodStavkaFloor = round(100 * $dopolnitelnyDohodStavka) / 100;
			}

			if ($CALC['ref_konec'] <= $CALC['ref_nachalo']) {
				$dopolnitelnyDohod = 'без дополнительных процентов';
			} else {
				$dopolnitelnyDohod = $CALC['summa'] * ($dopolnitelnyDohodStavka / 100) * ($srokInDays / 360);
			}
						
			//F::debug($CALC);
		}
	
		$out = 'Вклад <b>"'.$vklad['vklad_name'].'"</b>';
		$out .= '<br>Сумма вклада: <b>'.$this->format_result($CALC['summa'], $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b>';
		$out .= '<br>Сумма процентов на день наступления срока возврата вклада: <b>'.$this->format_result($PROC, $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b>';
		if($Premial>0) $out .= '<br>Сумма премиальных процентов: <b>'.$this->format_result($Premial, $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b>';
		
		if ($CALC['ref_konec'] <= $CALC['ref_nachalo']) {
			if ($dopolnitelnyDohod) $out .=  '<br>Ставка дополнительных процентов: <strong>'.$dopolnitelnyDohodStavkaFloor.'</strong>';
			if ($dopolnitelnyDohod) $out .= '<br>Сумма дополнительных процентов: <strong>'.$dopolnitelnyDohod.'</strong>';
			$dopolnitelnyDohod = 0;
		} else {
			if ($dopolnitelnyDohod) $out .=  '<br>Ставка дополнительных процентов: <strong>'.$dopolnitelnyDohodStavkaFloor.'%</strong>';
			if ($dopolnitelnyDohod) $out .= '<br>Сумма дополнительных процентов: <strong>'.$this->format_result($dopolnitelnyDohod, $vklad['vklad_val']).' '.$vklad['vklad_val'].'</strong>';
		}
		
		//if($PREMIAL>0) $out .= '<br>Сумма премиальных процентов (вар2)- <b>'.$this->format_result($PREMIAL, $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b>';
		
		$out .='<br> Общая сумма на день возврата вклада: <b>'.$this->format_result($SUMM + $Premial + $dopolnitelnyDohod, $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b>';
		if ($CALC['podohod']==1) {
			$out .='<br> Сумма удержанного из суммы процентов подоходного налога: <b>'.$this->format_result($TUD).'</b>';
		}

		
		
		/*
		if ($CALC[vklad_id] == 62 || $CALC[vklad_id] == 63||$CALC[vklad_id] == 64 || $CALC[vklad_id] == 66){
			$o = $CALC['summa'];

			if ($CALC[vklad_id] == 62){
				$prem = 0.01;
			}
			else{
				$prem = 0.005;
			}
			
			$out = 'Вклад <b>"'.$vklad['vklad_name'].'"</b>';
			$out .= '<br>Сумма вклада: <b>'.$this->format_result($CALC['summa'], $vklad['vklad_val']).' '.$vklad['vklad_val'].'</b>';
			
			
			$out_raschet = "";
			
			$d = array(31, 31, 30, 31, 30, 31, 31, 28, 31, 30, 31, 30,);
			//$out_raschet = "<table border='1'>";
			for($i = 1;  $i < 13; $i++){
				$s = $o * $vklad[vklad_procent] / 100 / 365 * $d[($i-1)];
				$p += $o * $prem / 365 * $d[($i-1)];
				$o += $s;
			
				if ($CALC[vklad_id] == 62){
					$s = round($s,-1);
					$o = round($o,-1);
					$p = round($p,-1);
				}
				
				$out_raschet .= $i."<i>-ая капитализация:</i> сумма = <b>".number_format($s, 0, '.', ' ')." ".$vklad['vklad_val']."</b>; остаток = <b>".number_format($o, 0, '.', ' ')." ".$vklad['vklad_val']."</b>;<br>";
				if (($i == 3) || ($i == 6) || ($i == 9) || ($i == 12)){
					$o += $p;
					$summaPrem += $p;
					$out_raschet .= "Премиальные проценты ".($i / 3).": сумма = <b>".number_format($p, 0, '.', ' ')." ".$vklad['vklad_val']."</b>; остаток = <b>".number_format($o, 0, '.', ' ')." ".$vklad['vklad_val']."</b>;<br>";
					if ($CALC[vklad_id] == 62){
						$prem += 0.01;
					}
					else{
						$prem += 0.005;
					}
			
					$p = 0;
				}
			}
			$out .= '<br>Сумма основных процентов: <b>'.number_format(($o - $CALC['summa']-$summaPrem), 0, '.', ' ').' '.$vklad['vklad_val'].'</b>';
			$out .= '<br>Сумма премиальных процентов: <b>'.number_format($summaPrem, 0, '.', ' ').' '.$vklad['vklad_val'].'</b>';
			$out .= '<br>Сумма процентов на день наступления срока возврата вклада: <b>'.number_format(($o - $CALC['summa']), 0, '.', ' ').' '.$vklad['vklad_val'].'</b>';
			$out .= '<br>Общая сумма на день возврата вклада: <b>'.number_format($o, 0, '.', ' ').' '.$vklad['vklad_val'].'</b>';
			//$out_raschet .= "</table>";
		}
		*/
		
		$out .='<br><br><a id="show_raschet" style="cursor:pointer;">Показать расчет</a><div id="raschet" style="display:none;"><br><b>расчет</b><br>'.$out_raschet.'</div>';

	
		
		echo $out;
			
		//echo "<br> Длина периода <b>$days ".F::NumberEnd($days,'дней','день','дня').'</b>';
		//print '<pre>';			print_r($PERIOD);			print '</pre>';
		//F::debug($PERIOD);
		//F::debug($vklad);
		//F::debug($CALC);
	}catch (Exception $e){
		echo '<span style="color:red">'.$e->getMessage().'</span>';
		//F::debug($CALC);
	}


	}


	private function exception(Exception $e){
		echo $e->getMessage().' '.$e->getFile().' ('.$e->getLine().')';
	}

	private function date2days($date, $yar_days=365) {
		preg_match('~(....)(..)(..)~', $date, $m);
		$d['y']=$m[1];
		$d['m']=$m[2];
		$d['d']=$m[3];

		if($yar_days==365 || $yar_days==366){ // реальное количество дней в году
			$time = mktime(0,1,0,$d['m'],$d['d'],$d['y']);
			$d['days'] = round($time/(60*60*24) );
		}elseif ($yar_days==360){ // В году 360 дней, в КАЖДОМ месяце по 30
			//echo ' 360 ';
			$d['d'] = ($d['d']<=30)? $d['d'] : 30;
			$d['days'] = $d['y']*360+$d['m']*30+$d['d'];
		}

		return $d['days'];
	}

	private function DateDelta($date='2011-05-31', $delta = "+1y +5m +30d", $DayOts, $format='Ymd'){
		try{
			$D = preg_split('~ ~',$delta);
			foreach ($D as $dt) {
				preg_match('~(....)-*(..)-*(..)~',$date,$match);
				$y=$match[1]; $m=$match[2]; $d=$match[3];
				if(preg_match('~^([\+\-]{0,1})(\d+)([dmy]{1})$~',$dt,$match)){
					switch($match[3]){
						case 'd':	$d = $match[1]=='-' ? $d-$match[2] : $d+$match[2]; break;
						case 'm': 
							if ($match[1]=='-') {
							  $m = $m - $match[2];
							} else {
							  /*$lastDay = substr($date,6,2);
							  if (isset($DayOts) && $DayOts!=$lastDay) { $d = $DayOts; }
							  $m = $m + $match[2];
							  if ($m==13) { $m=01; $y++; }*/
							 
							  if (isset($DayOts) && ($DayOts > 28) && ($d < 4))
							  {
							      $d = $DayOts;
							  }
							  else
							  {
							      $m = $m + $match[2];
							  
								  while ($m > 12)
								  {
								       $m-=12;
								       $y+=1;
								  } 
							  }
							  /*$d = $d-1;
								if (checkdate($m, $d, $y) == FALSE) { 
									$d = $d-1;
									if (checkdate($m, $d, $y) == FALSE) { 
									   $d = $d-1;
									}
								}
							  }*/
							}
							break;
						case 'y':	$y = $match[1]=='-' ? $y-$match[2] : $y+$match[2]; break;
					}
					$t = mktime(0,0,1,$m,$d,$y,null);
					$date = date($format, $t);
				}
			}
			return $date;

		}catch(Exception $e){
			$this->exception($e);
		}
	}

	private function format_result($value, $val_key = 'BYN')
	{
		$res = $this->round($value, $val_key);
		return F::prn_number($res);
	}

	private function round($value, $val_key = 'BYN')
	{
		if($val_key == 'BYN'){
			// округляем до десятков
			$res = round($value,2);
			$res = number_format($res, 2, '.', '');
		}else{
			// для остальных валют округляем до сотых
			$res = sprintf("%01.2f",$value);
			$res = round(round(round($value,4),3),2);
		}
		return $res;
	}

	private function redirect($redirectURL, $debug = false){
		echo '<br><br><a href="'.$redirectURL.'">REDIRECT LINK</a>';
		if(!$debug) header("Location: ".$redirectURL); exit();
	}

	private function getParam($param){
		return REG::I()->PARAM[$param];
	}

	private function utf2win($text){
		return iconv('utf-8','cp1251',$text);
	}

	private function win2utf($text){
		return iconv('cp1251','utf-8',$text);
	}
}
?>
