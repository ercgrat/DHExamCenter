<?php //progress-fragments-tag_frequency

require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";

session_start();
session_regenerate_id();

if(!$_SESSION['user']->logged_in || !isset($_POST["progress-tag_frequency-classid"]))
{
    exit();
}

$classid = $_POST["progress-tag_frequency-classid"];

$xsl = new DOMDocument();
$xsl->load('xsl/donut_graph.xsl');
$xslt = new XSLTProcessor();
$xslt->importStylesheet($xsl);

$tag_xml = array();
$tag_names = array();

if(isset($_POST["progress-tag_frequency-userid"]))
{
    $user_tags_query = $db_handle->prepare("SELECT tag_results.tagid, SUM(tag_results.correct)/COUNT(tag_results.tagid) AS 'ratio', agstay.agnametay FROM (SELECT aglinkstay.agidtay AS 'tagid', uestionresultsquay.orrectcay AS 'correct' FROM uestionresultsquay INNER JOIN aglinkstay ON uestionresultsquay.uestionidquay = aglinkstay.uestionidquay WHERE uestionresultsquay.lassidcay = ? AND uestionresultsquay.seriduyay = ?) AS tag_results LEFT JOIN agstay ON agstay.agidtay = tag_results.tagid GROUP BY tagid ORDER BY ratio DESC");
    $user_tags_query->bind_param("ii", $classid, $_SESSION["user"]->id);
	$user_tags_query->execute();
	$user_tags_query->store_result();
	$user_tags_query->bind_result($tagid, $ratio, $tag);
	
	if($user_tags_query->num_rows() == 0) {
		echo "<p>No results found.</p>";
	} else {
		echo "<ul class='horizontal_list'>";
		while($user_tags_query->fetch()) {
			echo "<li class='horizontal_item'>";
			$xml = "<root><object ratio='$ratio'>You answered " . ($ratio*100)	. "% of questions about '$tag' correctly.</object></root>";
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$table_data = $xslt->transformToXml($doc);
			echo "<p>$tag</p><br/><p class='note'>you:</p>$table_data<br/>";
			
			$class_tags_query = $db_handle->prepare("SELECT SUM(tag_results.correct)/COUNT(tag_results.tagid) AS 'ratio' FROM (SELECT aglinkstay.agidtay AS 'tagid', uestionresultsquay.orrectcay AS 'correct' FROM uestionresultsquay INNER JOIN aglinkstay ON uestionresultsquay.uestionidquay = aglinkstay.uestionidquay WHERE uestionresultsquay.lassidcay = ? AND aglinkstay.agidtay = ?) AS tag_results");
			$class_tags_query->bind_param("ii", $classid, $tagid);
			$class_tags_query->execute();
			$class_tags_query->store_result();
			$class_tags_query->bind_result($ratio);
			$class_tags_query->fetch();
			
			$xml = "<root><object ratio='$ratio'>" . ($ratio*100)	. "% of students answered questions about '$tag' correctly.</object></root>";
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$table_data = $xslt->transformToXml($doc);
			echo "<p class='note'>your class:</p>$table_data";
			
			echo "</li>";
		}
		echo "</ul>";
	}
	
} else {
	$tags_query = $db_handle->prepare("SELECT tag_results.tagid, SUM(tag_results.correct)/COUNT(tag_results.tagid) AS 'ratio', agstay.agnametay FROM (SELECT aglinkstay.agidtay AS 'tagid', uestionresultsquay.orrectcay AS 'correct' FROM uestionresultsquay INNER JOIN aglinkstay ON uestionresultsquay.uestionidquay = aglinkstay.uestionidquay WHERE uestionresultsquay.lassidcay = ?) AS tag_results LEFT JOIN agstay ON agstay.agidtay = tag_results.tagid GROUP BY tagid ORDER BY ratio DESC");
	$tags_query->bind_param("i", $classid);
	$tags_query->execute();
	$tags_query->store_result();
	$tags_query->bind_result($tagid, $ratio, $tag);
	
	if($tags_query->num_rows() == 0) {
    echo "<p>No results found.</p>";
	} else {
		echo "<ul class='horizontal_list'>";
		while($tags_query->fetch()) {
			echo "<li class='horizontal_item'>";
			$xml = "<root identifier='$tagid'><object ratio='$ratio'>" . ($ratio*100)	. "% of students answered questions about '$tag' correctly.</object></root>";
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$table_data = $xslt->transformToXml($doc);
			echo "<p>$tag</p>$table_data";
			echo "</li>";
		}
		echo "</ul>";
	}
}

?>