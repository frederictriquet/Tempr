<?php if (!isset($is_editing)) $is_editing = false;?>
<td><?php echo $p->pk_meta_id; ?></td>
<td><?php echo $p->name; ?></td>
<td><?php
if ($is_editing) {
	echo '<input value="'.$p->string_val.'" ';
	echo 'onchange="updateMetaString('.$p->pk_meta_id.', this.value);" />';
} else
	echo $p->string_val;
?>
</td>

<td><?php
if ($is_editing) {
	echo '<input value="'.$p->int_val.'" ';
	echo 'onchange="updateMetaInt('.$p->pk_meta_id.', this.value);" />';
} else
	echo $p->int_val;
?>
</td>

<td><?php
if ($is_editing) {
	echo '<input value="'.$p->date_val.'" ';
	echo 'onchange="updateMetaDate('.$p->pk_meta_id.', this.value);" />';
} else
	echo $p->date_val;
?>
</td>

<td><?php echo $p->last_update; ?></td>

