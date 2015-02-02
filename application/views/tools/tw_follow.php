<?php $this->load->helper('asset');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>Twitter</title>
<link href="index.css" rel="stylesheet" />
<link href="jquery.tweet.css" rel="stylesheet">
<?php echo css_asset('jquery.tweet.css', 'tools');?>
<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jquery.tweet.js', 'tools');?>
<script>
    jQuery(document).ready(function($) {
      $(".tweet").tweet({
        join_text: "auto",
        username: "<?php echo $tweet;?>",
        avatar_size: 12,
        count: "<?php echo $count;?>",
        auto_join_text_default: "<?php echo $this->lang->line('we said');?>", 
        auto_join_text_ed: "<?php echo $this->lang->line('we');?>",
        auto_join_text_ing: "<?php echo $this->lang->line('we were');?>",
        auto_join_text_reply: "<?php echo $this->lang->line('we replied');?>",
        auto_join_text_url: "<?php echo $this->lang->line('we were checking out');?>",
        auto_join_text_seconds_ago: "<?php echo $this->lang->line('seconds ago');?>",
        auto_join_text_a_minute_ago: "<?php echo $this->lang->line('a minute ago');?>",
        auto_join_text_minutes_ago: "<?php echo $this->lang->line('minutes ago');?>",
        auto_join_text_an_hour_ago: "<?php echo $this->lang->line('an hour ago');?>",
        auto_join_text_hours_ago: "<?php echo $this->lang->line('hours ago');?>",
        auto_join_text_a_day_ago: "<?php echo $this->lang->line('a day ago');?>",
        auto_join_text_days_ago: "<?php echo $this->lang->line('days ago');?>",
        auto_join_text_about: "<?php echo $this->lang->line('about');?>",    
        loading_text: "<?php echo $this->lang->line('loading tweets');?>"
      });
    });
  </script>
</head>
<body>
<div class='tweet'></div>
</body>
</html>
