<?php

  class Ajax_pagination
  {
	public $total;
	public $anchors;
	
	function __construct()
	{
	  ///constructior
	}
	function __destruct()
	{
	  ////destructior
	}
	
	//calculate pagination data
	/*
	
	function calculate_paginationdata($counter,$result,$totalresult,$number,$to,$of,$msg)
	{
	  if($totalresult==0)
	  {
		 $message=$msg;
	  }
	  else
	  {
		 if($counter <= 1)
		 {  
			$stcounter=1;
			$tocounter=$result;
			$ofcounter=$totalresult;
			$message=$stcounter.' '.$to.' '.$tocounter.' '.$of.' '.$totalresult;
		 }
		 else
		 {
			$stcounter=($counter-1)*$number;
			$tocounter=($counter-1)*$number+$result;
			$ofcounter=$totalresult;
			$message=$stcounter.' '.$to.' '.$tocounter.' '.$of.' '.$totalresult;
		 
		 }
	  
	  }
	  return $message;
	}
	*/
	function make_search($numrows,$starting,$recpage,$first_lb,$last_lb,$previous_lb,$next_lb,$page_lb,$of_lb,
	 $total_lb,$page_p,$div_p,$str_post)
	{
			//ajax pagination preparation
			$next           =    $starting+$recpage;
			$var            =    ((intval($numrows/$recpage))-1)*$recpage;
			$page_showing   =    intval($starting/$recpage)+1;
			$total_page     =    ceil($numrows/$recpage);

			if($numrows % $recpage != 0){
				$last = ((intval($numrows/$recpage)))*$recpage;
			}else{
				$last = ((intval($numrows/$recpage))-1)*$recpage;
			}
			
			/*ajax funcition js parrams
			* url,divname,starting,string post
			*/
			//calculate previous link
			$previous = $starting-$recpage;
			$anc = "<div class='pagination'><ul class='pagination'>";
			if($previous < 0){
				$anc .= "<li class='previous-off'><a href='javascript:void(0)'>".$first_lb."</a></li>";
				$anc .= "<li class='previous-off'><a href='javascript:void(0)'>".$previous_lb."</a></li>";
			}else{
				$anc .= "<li class='next'><a href='javascript:void(0)' onclick=\"javascript:load_pagination('$page_p','$div_p','0','$str_post');\">".$first_lb." </a></li>";
				$anc .= "<li class='next'><a href='javascript:void(0)' onclick=\"javascript:load_pagination('$page_p','$div_p','$previous','$str_post');\">".$previous_lb." </a></li>";
			}
			
			################If you dont want the numbers just comment this block###############    
			$norepeat = 4;//no of pages showing in the left and right side of the current page in the anchors 
			$j = 1;
			$anch = "";
			for($i=$page_showing; $i>1; $i--){
				$fpreviousPage = $i-1;
				$page = ceil($fpreviousPage*$recpage)-$recpage;
				$anch = "<li><a href='javascript:void(0)' onclick=\"javascript:load_pagination('$page_p','$div_p','$page','$str_post');\" >$fpreviousPage </a></li>".$anch;
				if($j == $norepeat) break;
				$j++;
			}
			$anc .= $anch;
			$anc .= "<li class='active'><a href='javascript:void(0)'>".$page_showing."</a></li>";
			$j = 1;
			for($i=$page_showing; $i<$total_page; $i++){
				$fnextPage = $i+1;
				$page = ceil($fnextPage*$recpage)-$recpage;
				$anc .= "<li><a href='javascript:void(0)' onclick=\"javascript:load_pagination('$page_p','$div_p','$page','$str_post');\" >$fnextPage</a></li>";
				if($j==$norepeat) break;
				$j++;
			}
			############################################################
			if($next >= $numrows){
				$anc .= "<li class='previous-off'><a href='javascript:void(0)'>".$next_lb."</a></li>";
				$anc .= "<li class='previous-off'><a href='javascript:void(0)'>".$last_lb."</a></li>";
			}else{
				$anc .= "<li class='next'><a onclick=\"javascript:load_pagination('$page_p','$div_p','$next','$str_post');\" href='javascript:void(0)'>".$next_lb." </a></li>";
				$anc .= "<li class='next'><a href='javascript:void(0)' onclick=\"javascript:load_pagination('$page_p','$div_p','$last','$str_post');\">".$last_lb."</a></li>";
			}
				$anc .= "</ul></div>";
				
			//assaign anchors to the public accessable variable
			$this->anchors = $anc;
			//assaign total record details
			$this->total = "".$page_lb." : $page_showing <i> ".$of_lb."  </i> $total_page . ".$total_lb.": $numrows";
	  } 
	 
	 //second pagination for refrences
	 function make_search2($numrows,$starting,$recpage,$first_lb,$last_lb,$previous_lb,$next_lb,$page_lb,$of_lb,
	 $total_lb,$page_p,$div_p,$str_post,$fieldname)
	{
	
			//ajax pagination preparation
			$next           =    $starting+$recpage;
			$var            =    ((intval($numrows/$recpage))-1)*$recpage;
			$page_showing   =    intval($starting/$recpage)+1;
			$total_page     =    ceil($numrows/$recpage);

			if($numrows % $recpage != 0){
				$last = ((intval($numrows/$recpage)))*$recpage;
			}else{
				$last = ((intval($numrows/$recpage))-1)*$recpage;
			}
			
			/*ajax funcition js parrams
			* url,divname,starting,string post
			*/
			//calculate previous link
			$previous = $starting-$recpage;
			$anc = "<ul id='pagination-flickr'>";
			if($previous < 0){
				$anc .= "<li class='previous-off'>".$first_lb."</li>";
				$anc .= "<li class='previous-off'>".$previous_lb."</li>";
			}else{
				$anc .= "<li class='next'><a href='javascript:void(0)' onclick=\"javascript:load_pagination2('$page_p','$div_p','0','$str_post','$fieldname');\">".$first_lb." </a></li>";
				$anc .= "<li class='next'><a href='javascript:void(0)' onclick=\"javascript:load_pagination2('$page_p','$div_p','$previous','$str_post','$fieldname');\">".$previous_lb." </a></li>";
			}
			
			################If you dont want the numbers just comment this block###############    
			$norepeat = 4;//no of pages showing in the left and right side of the current page in the anchors 
			$j = 1;
			$anch = "";
			for($i=$page_showing; $i>1; $i--){
				$fpreviousPage = $i-1;
				$page = ceil($fpreviousPage*$recpage)-$recpage;
				$anch = "<li><a href='javascript:void(0)' onclick=\"javascript:load_pagination2('$page_p','$div_p','$page','$str_post','$fieldname');\" >$fpreviousPage </a></li>".$anch;
				if($j == $norepeat) break;
				$j++;
			}
			$anc .= $anch;
			$anc .= "<li class='active'>".$page_showing."</li>";
			$j = 1;
			for($i=$page_showing; $i<$total_page; $i++){
				$fnextPage = $i+1;
				$page = ceil($fnextPage*$recpage)-$recpage;
				$anc .= "<li><a href='javascript:void(0)' onclick=\"javascript:load_pagination2('$page_p','$div_p','$page','$str_post','$fieldname');\" >$fnextPage</a></li>";
				if($j==$norepeat) break;
				$j++;
			}
			############################################################
			if($next >= $numrows){
				$anc .= "<li class='previous-off'>".$next_lb."</li>";
				$anc .= "<li class='previous-off'>".$last_lb."</li>";
			}else{
				$anc .= "<li class='next'><a onclick=\"javascript:load_pagination2('$page_p','$div_p','$next','$str_post','$fieldname');\" href='javascript:void(0)'>".$next_lb." </a></li>";
				$anc .= "<li class='next'><a href='javascript:void(0)' onclick=\"javascript:load_pagination2('$page_p','$div_p','$last','$str_post','$fieldname');\">".$last_lb."</a></li>";
			}
				$anc .= "</ul>";
				
			//assaign anchors to the public accessable variable
			$this->anchors = $anc;
			//assaign total record details
			$this->total = "".$page_lb." : $page_showing <i> ".$of_lb."  </i> $total_page . ".$total_lb.": $numrows";
	  } 
      
    function make_search_view($numrows,$starting,$recpage,$first_lb,$last_lb,$previous_lb,$next_lb,$page_lb,$of_lb,
    $total_lb,$page_p,$div_p,$str_post)
    {
    
            //ajax pagination preparation
            $next           =    $starting+$recpage;
            $var            =    ((intval($numrows/$recpage))-1)*$recpage;
            $page_showing   =    intval($starting/$recpage)+1;
            $total_page     =    ceil($numrows/$recpage);

            if($numrows % $recpage != 0){
                $last = ((intval($numrows/$recpage)))*$recpage;
            }else{
                $last = ((intval($numrows/$recpage))-1)*$recpage;
            }
            
            /*ajax funcition js parrams
            * url,divname,starting,string post
            */
            //calculate previous link
            $previous = $starting-$recpage;
            $anc = "<ul id='pagination-flickr'>";
            if($previous < 0){
                $anc .= "<li class='previous-off'>".$first_lb."</li>";
                $anc .= "<li class='previous-off'>".$previous_lb."</li>";
            }else{
                $anc .= "<li class='next'><a href='".$page_p."/0'>".$first_lb." </a></li>";
                $anc .= "<li class='next'><a href='".$page_p."/".$previous."' >".$previous_lb." </a></li>";
            }
            
            ################If you dont want the numbers just comment this block###############    
            $norepeat = 4;//no of pages showing in the left and right side of the current page in the anchors 
            $j = 1;
            $anch = "";
            for($i=$page_showing; $i>1; $i--){
                $fpreviousPage = $i-1;
                $page = ceil($fpreviousPage*$recpage)-$recpage;
                $anch = "<li><a href='".$page_p."/".$page."' >$fpreviousPage </a></li>".$anch;
                if($j == $norepeat) break;
                $j++;
            }
            $anc .= $anch;
            $anc .= "<li class='active'>".$page_showing."</li>";
            $j = 1;
            for($i=$page_showing; $i<$total_page; $i++){
                $fnextPage = $i+1;
                $page = ceil($fnextPage*$recpage)-$recpage;
                $anc .= "<li><a href='".$page_p."/".$page."' >$fnextPage</a></li>";
                if($j==$norepeat) break;
                $j++;
            }
            ############################################################
            if($next >= $numrows){
                $anc .= "<li class='previous-off'>".$next_lb."</li>";
                $anc .= "<li class='previous-off'>".$last_lb."</li>";
            }else{
                $anc .= "<li class='next'><a href='".$page_p."/".$next."'>".$next_lb." </a></li>";
                $anc .= "<li class='next'><a href='".$page_p."/".$last."'>".$last_lb."</a></li>";
            }
                $anc .= "</ul>";
                
            //assaign anchors to the public accessable variable
            $this->anchors = $anc;
            //assaign total record details
            $this->total = "".$page_lb." : $page_showing <i> ".$of_lb."  </i> $total_page . ".$total_lb.": $numrows";
      }   
	 
	 
	 
  }
?>