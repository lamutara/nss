<?php
class Node {
	Public $Seq;
	Public $RootSeq;
	Public $ParentSeq;
	Public $PostDate;
	Public $Text;
	Public $TextParent1;
	Public $TextParent2;
	Public $Del;
	Public $Like;
	Public $Tag;
	Public $New;
	Private $mysqli;

	public function __construct($mysqli) {
		$this->mysqli = $mysqli;
	}

	public function setBySeq($Seq) {
		$sql = <<<EOT
SELECT A.Seq
     , A.RootSeq
     , A.ParentSeq
     , A.PostDate
     , A.Text
     , A.Del
     , A.`Like`
  FROM Contents A
 WHERE A.Seq = $Seq;
EOT;
		if ($result = $this->mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$this->Seq = $row['Seq'];
				$this->RootSeq = $row['RootSeq'];
				$this->ParentSeq = $row['ParentSeq'];
				$this->PostDate = $row['PostDate'];
				$this->Text = $row['Text'];
				$this->Del = $row['Del'];
				$this->Like = $row['Like'];

				//new
				global $TIME_LAST_VIEW;
				if (isset($TIME_LAST_VIEW) and strtotime($this->PostDate) > $TIME_LAST_VIEW) {
					$this->New = True;
				} else {
					$this->New = False;
				}
			}
		}

		$this->setTagBy($Seq);
	}

	private function setTagBy($Seq) {
		$sql = <<<EOT
SELECT Tag
  FROM CtTag
 WHERE Seq = $Seq
 LIMIT 7;
EOT;
		if ($result = $this->mysqli->query($sql)) {
			$this->Tag = array();
			while ($row = $result->fetch_assoc()) {
				$this->Tag[] = $row['Tag'];
			}
		}
	}
	public function setBySeqWithTextParent($Seq) {
$sql = <<<EOT
SELECT
    A.Seq
  , A.RootSeq
  , A.ParentSeq
  , A.PostDate
  , A.Del
  , A.Like
  , A.Text Text
  , B.Text TextParent1
  , C.Text TextParent2
  FROM Contents A
  LEFT JOIN Contents B
    ON B.Seq = A.ParentSeq
  LEFT JOIN Contents C
    ON C.Seq = B.ParentSeq
 WHERE A.Seq = $Seq;
EOT;

		if ($result = $this->mysqli->query($sql)) {
			if ($row = $result->fetch_assoc()) {
				$this->Seq = $row['Seq'];
				$this->RootSeq = $row['RootSeq'];
				$this->ParentSeq = $row['ParentSeq'];
				$this->PostDate = $row['PostDate'];
				$this->Text = $row['Text'];
				$this->TextParent1 = $row['TextParent1'];
				$this->TextParent2 = $row['TextParent2'];
				$this->Del = $row['Del'];
				$this->Like = $row['Like'];
			}
		}

		$this->setTagBy($Seq);
	}

	public function urlRes($abs = false, $allQuery = true) {
		global $PAGE_URL;
		global $PAGE_URL_ABS;
		global $PAGE_URL_QUERY_ARRAY;

		if ($abs) {
			$url = $PAGE_URL_ABS;
		} else {
			$url = $PAGE_URL;
		}

		if ($allQuery) {
			$urlQueryArray = $PAGE_URL_QUERY_ARRAY;
		}
		$urlQueryArray["res"] = "1";
		$urlQueryArray["seq"] = $this->Seq;

		return $url.'?'.http_build_query($urlQueryArray);
	}

	public function urlLike($abs = false, $allQuery = true) {
		global $PAGE_URL;
		global $PAGE_URL_ABS;
		global $PAGE_URL_QUERY_ARRAY;

		if ($abs) {
			$url = $PAGE_URL_ABS;
		} else {
			$url = $PAGE_URL;
		}

		if ($allQuery) {
			$urlQueryArray = $PAGE_URL_QUERY_ARRAY;
		}
		$urlQueryArray["like"] = "1";
		$urlQueryArray["seq"] = $this->Seq;

		return $url.'?'.http_build_query($urlQueryArray);
	}

	public function urlTag($tag, $abs = false, $allQuery = true) {
		global $PAGE_URL;
		global $PAGE_URL_ABS;
		global $PAGE_URL_QUERY_ARRAY;

		if ($abs) {
			$url = $PAGE_URL_ABS;
		} else {
			$url = $PAGE_URL;
		}

		if ($allQuery) {
			$urlQueryArray = $PAGE_URL_QUERY_ARRAY;
		}
		$urlQueryArray["tag"] = $tag;

		return $url.'?'.http_build_query($urlQueryArray);
	}

	public function GetText() {
		$url = $this->urlRes();

		$text = '';

		$text .= '<tt>';
		$text .= "<a href=\"$url\" title=\"レス\" style=\"text-decoration: none;\" rel=\"nofollow\">■</a> ";
		$text .= $this->PostDate;

		foreach ($this->Tag as $tag) {
			$text .= '<small> <a style="text-decoration:none;" href="'.$this->urlTag($tag).'">#'.htmlspecialchars($tag).'</a></small>';
		}

		$text .= '<br>';

		if ($this->Del == 2) {
			$text .= '<i>流されました</i>';
		} elseif (abs($this->Del) == 3) {
			$text .= '<font size="+3">'.htmlspecialchars($this->Text).'</font>';
		} else {
			$text .= '<font color="#aaaaaa">';
			if (!empty($this->TextParent2)) {
				//HTML関連文字エスケープ
				$text2 = htmlspecialchars($this->TextParent2);
				$text2 = preg_replace('/^/mu', '> > ', $text2);
				$text2 = preg_replace('/\n/u', '<br>', $text2);
				$text .= $text2.'<br>';
			}
			if (!empty($this->TextParent1)) {
				$text1 = htmlspecialchars($this->TextParent1);
				$text1 = preg_replace('/^/mu', '> ', $text1);
				$text1 = preg_replace('/\n/u', '<br>', $text1);
				$text .= $text1.'<br><br>';
			}
			$text .= '</font>';

			$text0 = htmlspecialchars($this->Text);
			$text0 = preg_replace('/\n/u', '<br>', $text0);
			$text .= $text0;
		}

		$text = preg_replace('/((https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/u', '<a href="$1" target="_blank">$1</a>', $text);
		$text .= '<br></tt>';

		return $text;
	}

	public function GetTextFeed() {

		$text = '';

		$text .= "■ ";
		$text .= $this->PostDate.'<br />';

		foreach ($this->Tag as $tag) {
			$text .= '<small> <a style="text-decoration:none;" href="'.$this->urlTag($tag).'">#'.htmlspecialchars($tag).'</a></small>';
		}

		if ($this->Del == 2 and false) {
			$text .= '流されました';
		} else {
			if (!empty($this->TextParent2)) {
				//HTML関連文字エスケープ
				$text2 = htmlspecialchars($this->TextParent2);
				$text2 = preg_replace('/^/mu', '> > ', $text2);
				$text2 = preg_replace('/\n/u', '<br />', $text2);
				$text .= $text2.'<br />';
			}
			if (!empty($this->TextParent1)) {
				//HTML関連文字エスケープ
				$text1 = htmlspecialchars($this->TextParent1);
				$text1 = preg_replace('/^/mu', '> ', $text1);
				$text1 = preg_replace('/\n/u', '<br />', $text1);
				$text .= $text1.'<br /><br />';
			}
			//HTML関連文字エスケープ
			$text0 = htmlspecialchars($this->Text);
			$text0 = preg_replace('/\n/u', '<br />', $text0);
			$text .= $text0;
		}

		return $text;
	}

	public function GetSubTree() {

$sql = <<<EOT
SELECT Seq FROM Contents WHERE ParentSeq = '$this->Seq' ORDER BY PostDate
EOT;
		$root = $this->GetStrLine();
		$subTree = '';
		if ($result = $this->mysqli->query($sql)) {
			$nr = $result->num_rows;

			if ($nr > 0) {
				$root = preg_replace('/("Root1">　)/u', '"Root">│', $root);
			}else {
				$root = preg_replace('/("Root1">　)/u', '"Root">　', $root);
			}

			while ($row = $result->fetch_assoc()) {
				$childNode = new Node($this->mysqli);
				$childNode->setBySeq($row['Seq']);
				$str = $childNode->GetSubTree();

				$nr -= 1;
				if ($nr > 0) {
					$str = preg_replace('/("Edge">)/u', '$1│', $str);
					$str = preg_replace('/("Root">)/u', '"Edge">│', $str, 1);
					$str = preg_replace('/("Root">)/u', '"Edge">├', $str, 1);
					$str = preg_replace('/("Root">)/u', '"Edge">│', $str);
				} else {
					$str = preg_replace('/("Edge">)/u', '$1　', $str);
					$str = preg_replace('/("Root">)/u', '"Edge">│', $str, 1);
					$str = preg_replace('/("Root">)/u', '"Edge">└', $str, 1);
					$str = preg_replace('/("Root">)/u', '"Edge">　', $str);
				}

				$subTree .= $str;
			}
		}

		return $root.$subTree;
	}

	public function GetStrLine($res = FALSE){
		$str = '';

		if ($this->New) {
			$str .= '<span class="new">';
		} else {
			$str .= '<span>';
		}
	    //$str .= '<p>';
	    $str .= '';
        $str .= '<tt><span class="Root"></span></tt><br />';
        $str .= '<tt><span class="Root"></span>';

        $text = $this->Text;

        //応急処置：xrea環境だとbind_paramで ' が \' に変換される？
//        $text = preg_replace('/\\\(.)/u', '$1', $text);

        //HTML関連文字エスケープ
        $text = htmlspecialchars($text);

        $text = preg_replace('/((https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/u', '<a href="$1" target="_blank">$1</a>', $text);

        global $TAG_PATTERN;
        preg_match_all($TAG_PATTERN, $text, $out, PREG_PATTERN_ORDER);
        $tags = $out[1];
        $tags = array_unique($tags);
        foreach ($tags as $tag) {
        	$text = preg_replace('/([#＃]'.preg_quote($tag).')/u', '<a href="'.$this->urlTag($tag).'">$1</a>', $text);
        }

        $urlRes = $this->urlRes();
		$urlLike = $this->urlLike();

        $textLines = explode(PHP_EOL, $text);

        if (abs($this->Del) == 3) {
        	$str .= '<a href="#" title="'.htmlspecialchars($this->Text).'" style="text-decoration: none;">■</a>';
        	$str .= ' '.'<font size="+3"><b>'.htmlspecialchars($this->Text).' </b></font>';
        } elseif ($this->Del == 2 and !$res) {
			$str .= '<a href="'.$urlRes.'" title="'.htmlspecialchars($this->Text).'" style="text-decoration: none;" rel="nofollow">■</a>';
        	$str .= ' '.'<i>'.'流されました'.' </i>';
//        } elseif ($this->Del < 0) {
//        	$str .= '<a href="#" title="'.htmlspecialchars($this->Text).'" style="text-decoration: none;">■</a>';
//        	$str .= ' '.$textLines[0];
        } else {
			$str .= '<a href="'.$urlRes.'" title="レス" style="text-decoration: none;" rel="nofollow">■</a>';
        	$str .= ' '.$textLines[0];
        }


        $str .= ' </tt>';

        $checked = '';
        if ($res) {
        	$checked = 'checked';
        }
        if (!empty($this->Del) and abs($this->Del) == 3) {
        	$str .= " <input type=\"radio\" name=\"seq\" value=\"$this->Seq\" style=\"width:0.6em; height:0.6em;\" disabled>";
        } else {
        	$str .= " <input type=\"radio\" name=\"seq\" value=\"$this->Seq\" id=\"rb$this->Seq\" style=\"margin: 0; width:0.8em; height:0.8em;\" $checked >";
        }

        $str .= "<label for=\"rb$this->Seq\"><font size=\"-3\"> $this->PostDate</font></label>";

        $str .= '<font size="-1">';

       	foreach ($this->Tag as $tag) {
       		$str .= ' <a style="text-decoration:none;" href="'.$this->urlTag($tag).'">#'.htmlspecialchars($tag).'</a> ';
       	}

		if (!empty($this->Like) and $this->Like > 0) {
			if (!empty($this->Del) and abs($this->Del) == 3) {
	    		$str .= ' ★';
	    		$str .= '<b>'.$this->Like.'</b> <font size="-1">いいね！</font>';
			} else {
		    	$str .= ' <a href="'.$urlLike.'" title="いいね！" style="text-decoration: none;" rel="nofollow">★</a>';
		    	$str .= '<b>'.$this->Like.'</b> <font size="-1">いいね！</font>';
			}
		} else {
			if (!empty($this->Del) and abs($this->Del) == 3) {
				;
			} else {
	    		$str .= ' <a href="'.$urlLike.'" title="いいね！" style="text-decoration: none;" rel="nofollow">☆</a>';
			}
		}
       	$str .= '</font>';
	    $str .= '';
	    $str .= '<br>';

	    if ( abs($this->Del) == 3 or (!$res and $this->Del == 2)) {
			return $str;
	    }

	    $text = '';
	    $skip = true;
		foreach ($textLines as $text) {
			if ($skip) {
				$skip = false;
				continue;
			}
			$str .= '<tt><span class="Root1">　 '.$text.'</span></tt><br>';
		}

		$str .= '</span>'; //content

		return $str;
	}

	public function GetStrLineQuote($res = FALSE){
		$str = '';

		$str .= '<table><tr><td valign="top"><font size="+3">“</font></td><td>';
        $str .= '<div>';

        $text = $this->Text;

        //HTML関連文字エスケープ
        $text = htmlspecialchars($text);

        $text = preg_replace('/((https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/u', '<a href="$1" target="_blank">$1</a>', $text);

        global $TAG_PATTERN;
        preg_match_all($TAG_PATTERN, $text, $out, PREG_PATTERN_ORDER);
        $tags = $out[1];
        $tags = array_unique($tags);
        foreach ($tags as $tag) {
        	$text = preg_replace('/([#＃]'.preg_quote($tag).')/u', '<a href="'.$this->urlTag($tag).'">$1</a>', $text);
        }

        $urlRes = $this->urlRes();
		$urlLike = $this->urlLike();

        $textLines = explode(PHP_EOL, $text);


        $text = '';
        foreach ($textLines as $text) {
        	$str .= '<tt><span class="Root1">'.$text.'</span></tt><br>';
        }
        $str .= '</div>';



        $str .= '<div align="right" style="padding-left: 6em; margin-top:0.5em;">';
        $str .= '<font size="-3">';

        $str .= '――';

        /*
        $checked = '';
        if ($res) {
        	$checked = 'checked';
        }
        if (!empty($this->Del) and abs($this->Del) == 3) {
        	$str .= " <input type=\"radio\" name=\"seq\" value=\"$this->Seq\" style=\"width:0.6em; height:0.6em;\" disabled>";
        } else {
        	$str .= " <input type=\"radio\" name=\"seq\" value=\"$this->Seq\" id=\"rb$this->Seq\" style=\"margin: 0; width:0.8em; height:0.8em;\" $checked >";
        }

        $str .= "<label for=\"rb$this->Seq\"><font size=\"-3\"> $this->PostDate</font></label>";
        */

       	foreach ($this->Tag as $tag) {
       		$str .= ' <a style="text-decoration:none;" href="'.$this->urlTag($tag).'">#'.htmlspecialchars($tag).'</a> ';
       	}

        $str .= '<a href="'.$urlRes.'" title="レス" style="text-decoration: none;" rel="nofollow">■</a>';

		if (!empty($this->Like) and $this->Like > 0) {
			if (!empty($this->Del) and abs($this->Del) == 3) {
	    		$str .= ' ★';
	    		$str .= '<b>'.$this->Like.'</b> <font size="-2">いいね！</font>';
			} else {
		    	$str .= ' <a href="'.$urlLike.'" title="いいね！" style="text-decoration: none;" rel="nofollow">★</a>';
		    	$str .= '<b>'.$this->Like.'</b> <font size="-2">いいね！</font>';
			}
		} else {
			if (!empty($this->Del) and abs($this->Del) == 3) {
				;
			} else {
	    		$str .= ' <a href="'.$urlLike.'" title="いいね！" style="text-decoration: none;" rel="nofollow">☆</a>';
			}
		}
       	$str .= '</font>';
        $str .= '</div>';
	    $str .= '';


	    $str .= '</div>'; //content
		$str .= '</td><td valign="top"><font size="+3">”</font></td></tr></table>';

		return $str;
	}

}

?>