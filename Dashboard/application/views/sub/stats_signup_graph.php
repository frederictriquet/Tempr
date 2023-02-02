<script language="JavaScript">
$(document).ready(function() {	
   var title = {
      text: 'Inscriptions'
   };
   var xAxis = {
      categories: <?php echo json_encode($month["day"]); ?>
   };
   var yAxis = {
      title: {
         text: "Nb"
      },
      plotLines: [{
         value: 0,
         width: 1,
         color: '#FF6666'
      }]
   };
   var series =  [{
       name: 'Inscriptions',
       data:  <?php echo json_encode($month["user"]); ?>, color :'#FF6666'
   }];
   var json = {};
   json.title = title;
   json.xAxis = xAxis;
   json.yAxis = yAxis;
   json.series = series;
   $('#courbeSignUp').highcharts(json);
});
</script>