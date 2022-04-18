<?php
require_once 'libs/init.php'; 
if(!authenticated()){
  header("Location:login.php");
  exit;
}
?>
<!DOCTYPE HTML>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <style>
    html, body {
      width: 100%;
      height: 100%;
      overflow: hidden;
      margin: 0px;
      background-color: #EBEBEB;
    }
  </style>
  <link rel="stylesheet" type="text/css" href="assets/libs/dhtmlx/dhtmlx.css"/>
  <link rel="stylesheet" type="text/css" href="assets/fonts/font_awesome/css/font-awesome.min.css"/>
  <link rel="stylesheet" type="text/css" href="assets/fonts/font_roboto/roboto.css"/>

  <title>Plant Maintenance - P2</title>

  
  <script src="assets/js/date-utils.js"></script>
  <script src="assets/libs/dhtmlx/dhtmlx.js"></script>
  <script>

     var rootToolbar,myLayout2, myTree, myGrid, myFolders, myMenu, myToolbar,myAcc;
     	var eventIndex = 1;
	 
	 skin = "dhx_skyblue";
	 var iconsPath = {
			dhx_skyblue: "imgs",
			dhx_web: "icons_web",
			dhx_terrace: "icons_terrace"
		};
			
function doOnLoad(){

		rootLayout = new dhtmlXLayoutObject({
					parent: document.body,
					pattern: "2U",
					cells: [
					{id: "a", text: "Main Menu", width: 240, header: true },
					{id: "b", text: " "},
							

				]
			});

		rootLayout.cells("a").setCollapsedText("Main Menu");
		rootLayout.cells("b").hideHeader();
				
		 rootToolbar = rootLayout.attachToolbar({
          items: [
            {type: 'text', text: '<div style="font-size: 15px;font-weight: bold;">Plant Maintenance</div>'},
            {type: 'spacer'},
            {
              type: 'buttonSelect', id: 'user', text: '<?php echo $_SESSION["full_name"]; ?>', options: [
                {type: 'button', id: 'change-password', text: 'Ubah Password'},
                {type: 'button', id: 'logout', text: 'Log Out'}
              ]
            }
          ]
        });

		rootToolbar.attachEvent("onClick", function(id){
    		if(id == 'logout') {
    			window.location.href = 'logout.php';
    		}
		});
		
		myTabbar = rootLayout.cells("b").attachTabbar({
				tabs: [
					{ id: "Dashboard", text: "Dashboard", active: true },
				]
		});
		
		myTabbar.tabs('Dashboard').attachURL('dashboard.php');
			
		myTree = rootLayout.cells("a").attachTree();
		myTree.setImagePath("assets/libs/dhtmlx/imgs/dhxtree_web/");
		myTree.load("libs/treemenu.xml");
		myTree.openAllItems(0);
		myTree.setOnClickHandler(tonclick);
		
			
   }

function tonclick(id) {
	var aurl=myTree.getUserData(id,"href");
	if (aurl.lenght!=0) {
		rootLayout.cells("b").setText(myTree.getItemText(id));

		var jdl=myTree.getSelectedItemId();
		var ids = myTabbar.getAllTabs();
		var ix=0;
		for (var q=0; q<ids.length; q++) {
			if(jdl==ids[q]){
				ix=ix+1;
			}
		}
	
		if(ix>0){
			myTabbar.tabs(myTree.getSelectedItemId()).setActive();
		} else {
			myTabbar.addTab(myTree.getSelectedItemId(),myTree.getItemText(id),null, null, null, true);
			myTabbar.tabs(myTree.getSelectedItemId()).setActive();
			myTabbar.tabs(myTree.getSelectedItemId()).attachURL(aurl);
		}
		rootLayout.items[1].progressOff();
	}
};

var vukuran = window.screen.width;
if(vukuran <= 500) {
	window.open('mob/index.php');
}
			
 
  </script>
</head>
<body onload="doOnLoad();">
</body>
</html>
