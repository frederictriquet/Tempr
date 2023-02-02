<div class="panel panel-default floatingblock">
    <div class="panel-body">
        <div id="courbeSignUp" style="width: 700px; height: 450px; margin: 0 auto"></div>
        <?php $this->load->view('sub/stats_signup_graph', $month)?>
    </div>
</div>


<div class="panel panel-default floatingblock">
    <div class="panel-body">
        <div id="histoFriends" style="width: 700px; height: 450px; margin: 0 auto"></div>
        <?php $this->load->view('sub/stats_friends_graph', $this->data)?>
    </div>
</div>

