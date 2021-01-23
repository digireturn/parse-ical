function digireturn_google_calendar_parse_ical($content=''){
	$fields=array('DTSTART','DTEND','DTSTART;VALUE=DATE','DTEND;VALUE=DATE'/*,'DTSTAMP'*/,'UID','CREATED','DESCRIPTION','LAST-MODIFIED','LOCATION','SEQUENCE','STATUS','SUMMARY','TRANSP');
	$nl=strpos($content,"\r\n")!==FALSE?"\r\n":(
		strpos($content,"\n\r")!==FALSE?"\n\r":(
			strpos($content,"\n")!==FALSE?"\n":(
				strpos($content,"\r")!==FALSE?"\r":'-'
			)
		)
	);
	$content=str_replace($nl." ",'',$content);
	$head=substr($content,0,strpos($content,'BEGIN:VEVENT'));
	$content=substr($content,strpos($content,'BEGIN:VEVENT')+strlen('BEGIN:VEVENT'));
	$content=str_replace(array('END:VEVENT','END:VCALENDAR'),'',$content); 

	$ls=array();
	foreach(explode('BEGIN:VEVENT',$content) as $e){
		$o=array();
		foreach(explode("\n",$e) as $row){
			$key=sanitize_text_field(substr($row,0,strpos($row,':')));
			$value=sanitize_text_field(substr($row,strpos($row,':')+1));
			if(in_array($key,$fields)){
				if(in_array($key,array('DTSTART','DTEND','CREATED','LAST-MODIFIED')))$value=preg_replace('/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})[zZ]/','$1-$2-$3 $4:$5:$6',$value);
				if($key=='DTSTART;VALUE=DATE'||$key=='DTEND;VALUE=DATE'){
					$key=substr($key,0,strpos($key,';VALUE=DATE'));
					$value=preg_replace('/(\d{4})(\d{2})(\d{2})/','$1-$2-$3',$value);
				}
				$o[$key]=$value;
			}
		}
		$ls[]=$o;
	}
	return $ls;
}
