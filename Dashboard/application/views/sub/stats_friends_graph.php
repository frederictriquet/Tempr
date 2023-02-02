<script language="JavaScript">
$(document).ready(function() {
	var chart = {
			type: 'column'
	}
   var title = {
      text: 'Nombre d\'utilisateurs en fonction du nombre d\'amis'   
   };
   var xAxis = {
      categories: [
                    'Aujourd\'hui',
      				'La semaine dernière',
      				'Il y a 2 semaines',
      				'Il y a 1 mois'
      				],
  	crosshair: true
   };
   var yAxis = {
		min: 0,
		title: {
         text: 'Nombre d\'utilisateurs'
      }
   };
   var tooltip = {
       headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
       pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
           '<td style="padding:0"><b>{point.y:.0f} users</b></td></tr>',
       footerFormat: '</table>',
       shared: true,
       useHTML: true
   };
   var plotOptions = {
       column: {
           pointPadding: 0.2,
           borderWidth: 0
       }
   };
   var series =  [
      {
          name: '0 ami',
       	  data: <?php echo json_encode($fr_0); ?>
      }, 
      {
          name: '1-5 amis',
       	  data: <?php echo json_encode($fr_1_5); ?>
      }, 
      {
          name: '6-10 amis',
          data:  <?php echo json_encode($fr_6_10); ?>
      },
      {
          name: '11-15 amis',
          data:  <?php echo json_encode($fr_11_15); ?>
      },
      {
          name: '+16 amis',
          data:  <?php echo json_encode($fr_16_); ?>
      }];
   var json = {};
	json.chart = chart;
   json.title = title;
   json.xAxis = xAxis;
   json.yAxis = yAxis;
   json.tooltip = tooltip;
   json.plotOptions = plotOptions;
   json.series = series;

   $('#histoFriends').highcharts(json);
});
</script>