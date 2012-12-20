<?php
#    wizstats - bitcoin pool web statistics - 1StatsgBq3C8PbF1SJw487MEUHhZahyvR
#    Copyright (C) 2012  Jason Hughes <wizkid057@gmail.com>
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.


require_once 'config.php';

function block_table_start($sortable) {
	if ($sortable) {
		return "<TABLE id=\"blocklisttable\" CLASS=\"blocklist sortable\">";
	} else {
		return "<TABLE id=\"blocklisttable\" CLASS=\"blocklist\">";
	}
}

function block_table_end() {
	return "</TABLE>";
}

function block_table_header() {

	$blocks_header = "<thead>";
	$blocks_header .= "<TR id=\"blocklistheaderid\">";
	$blocks_header .= "<TH>Age</TH>";
	$blocks_header .= "<TH>Round Start</TH>";
	$blocks_header .= "<TH>Round Duration</TH>";
	$blocks_header .= "<TH>Accepted Shares</TH>";
	$blocks_header .= "<TH>Difficulty</TH>";
	$blocks_header .= "<TH>Luck</TH>";
	$blocks_header .= "<TH>Hashrate</TH>";
	$blocks_header .= "<TH>Confirmations</TH>";
	$blocks_header .= "<TH>Contributor</TH>";
	$blocks_header .= "<TH>Height</TH>";
	$blocks_header .= "<TH>Block Hash</TH>";
	$blocks_header .= "</TR>";
	$blocks_header .= "</thead>";
	return $blocks_header;

}

function block_table_row($row,$isodd) {

	$blocks_row = "";

	if (isset($row["acceptedshares"])) { $luck = 100 * ($row["network_difficulty"] / $row["acceptedshares"]); } else { $luck = 0; }
	if ($luck > 9999) { $luck = ">9999%"; } else { $luck = round($luck,2)."%"; }


	$roundstart = substr($row["roundstart"],0,19);
	if ($row["confirmations"] >= 120) { $confs = "Confirmed"; }
	else if ($row["confirmations"] == 0) { $confs = "Stale"; $luck = "n/a"; $roundstart = "<SMALL>(".substr($row["time"],0,19); $roundstart .= ")</SMALL>"; }
	else { $confs = $row["confirmations"]." of 120"; }

	$dbid = $row["blockid"];

	if ($row["confirmations"] == 0) { 
		$blocks_row .= "<TR id=\"blockrow$dbid\" BGCOLOR=\"#FFDFDF\" class=\"$isodd"."blockorphan\">"; 
	}
	else if ($row["confirmations"] >= 120) { 
		$blocks_row .= "<TR id=\"blockrow$dbid\" BGCOLOR=\"#DFFFDF\" class=\"$isodd"."blockconfirmed\">"; 
	}
	else { 
		$blocks_row .= "<TR class=\"$isodd\" id=\"blockrow$dbid\">";
	}

	$blocks_row .= "<TD sorttable_customkey=\"".$row["age"]."\">".prettyDuration($row["age"],false,1)."</TD>";



	$blocks_row .= "<TD>".$roundstart."</TD>";

	if (isset($row["duration"])) {
		list($seconds, $minutes, $hours) = extractTime($row["duration"]);
		$seconds = sprintf("%02d", $seconds);
		$minutes = sprintf("%02d", $minutes);
		$hours = sprintf("%02d", $hours);
		$blocks_row .= "<td style=\"width: 1.5em;  text-align: right;\">$hours:$minutes:$seconds</td>";

		$hashrate = ($row["acceptedshares"] * 4294967296) / $row["duration"];
		$hashrate = prettyHashrate($hashrate);

	} else {
		$blocks_row .= "<td style=\"text-align: right;\">n/a</td>";
		$hashrate = "n/a";
	}

	$blocks_row .= "<TD style=\"text-align: right;\" sorttable_customkey=\"".$row["acceptedshares"]."\">".$row["acceptedshares"]."</TD>";

	$blocks_row .= "<TD style=\"text-align: right;\">".round($row["network_difficulty"],0)."</TD>";
	$blocks_row .= "<TD style=\"text-align: right;\">".$luck."</TD>";


	$blocks_row .= "<TD style=\"text-align: right;\">".$hashrate."</TD>";




	$blocks_row .= "<TD class=\"blockconfirms\" style=\"text-align: right;\">".$confs."</TD>";
	if (isset($row['keyhash'])) {
		$fulladdress =  \Bitcoin::hash160ToAddress(bits2hex($row['keyhash']));
		$address = substr($fulladdress,0,10)."...";
	} else {
		$fulladdress = "";
		$address = "(Unknown user)"; 
	}
	$blocks_row .= "<TD><A HREF=\"userstats.php/".$fulladdress."\">".$address."</A></TD>";


	if ((isset($row["height"])) && ($row["height"] > 0)) {
		$ht = $row["height"];
	} else {
		$ht = "n/a";
	}
	$blocks_row .= "<TD style=\"text-align: right;\">$ht</TD>";

	$nicehash = "...".substr($row["blockhash"],40,24);
	$blocks_row .= "<TD><A HREF=\"http://blockchain.info/block/".$row["blockhash"]."\">".$nicehash."</A></TD>";
	$blocks_row .= "</TR>";

	return $blocks_row;


}

?>
