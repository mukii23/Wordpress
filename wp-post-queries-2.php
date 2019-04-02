/*******************
===== Insert ',' after each array item except last array item ======
*******************/

$array = array();
foreach( get_the_tags() as $tag ){
  $array[] = $tag->name;
}
echo implode(',' , $array);
