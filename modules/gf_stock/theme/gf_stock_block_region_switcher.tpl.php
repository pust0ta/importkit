<?php if ($regions): ?>
<ul>
<?php foreach($regions as $region_name => $region_link): ?>
  <li<?php if ($current_region == $region_name) 
              print ' class="active"'; ?>><?php print $region_link; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
