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
    $tags_query = $db_handle->prepare("SELECT tag_results.tagid, SUM(tag_results.correct)/COUNT(tag_results.tagid) AS 'ratio', agstay.agnametay FROM (SELECT aglinkstay.agidtay AS 'tagid', uestionresultsquay.orrectcay AS 'correct' FROM uestionresultsquay INNER JOIN aglinkstay ON uestionresultsquay.uestionidquay = aglinkstay.uestionidquay WHERE uestionresultsquay.lassidcay = ? AND uestionresultsquay.seriduyay = ?) AS tag_results LEFT JOIN agstay ON agstay.agidtay = tag_results.tagid GROUP BY tagid ORDER BY ratio DESC");
    $tags_query->bind_param("ii", $classid, $_SESSION["user"]->id);
}
else
{
    $tags_query = $db_handle->prepare("SELECT tag_results.tagid, SUM(tag_results.correct)/COUNT(tag_results.tagid) AS 'ratio', agstay.agnametay FROM (SELECT aglinkstay.agidtay AS 'tagid', uestionresultsquay.orrectcay AS 'correct' FROM uestionresultsquay INNER JOIN aglinkstay ON uestionresultsquay.uestionidquay = aglinkstay.uestionidquay WHERE uestionresultsquay.lassidcay = ?) AS tag_results LEFT JOIN agstay ON agstay.agidtay = tag_results.tagid GROUP BY tagid ORDER BY ratio DESC");
    $tags_query->bind_param("i", $classid);
}
$tags_query->execute();
$tags_query->store_result();
$tags_query->bind_result($tagid, $ratio, $tag);
if($tags_query->num_rows() == 0)
{
    echo "<p>No results found.</p>";
}
while($tags_query->fetch())
{
    array_push($tag_xml, "<root><object ratio='$ratio'>$tag</object></root>");
    array_push($tag_names, $tag);
}

echo "<table>";
$columns = 5;
$rows = (int)(count($tag_xml)/$columns);
if((count($tag_xml) % $columns) > 0) { $rows++; }

for($i = 0; $i < $rows; $i++)
{
    echo "<tr>";
    for($j = 0; $j < $columns; $j++)
    {
        $table_data = "";
        $index = $i*$columns + $j;
        $table_data = "";
        $xml = "";
        if($index < count($tag_xml))
        {
            $xml = $tag_xml[$index];
            $doc = new DOMDocument();
            $doc->loadXML($xml);
            $table_data = $xslt->transformToXml($doc);
        }
        
        echo "<td><p>$tag_names[$index]</p>$table_data</td>";
    }
    echo "</tr>";
}
echo "</table>";

?>