<?php if (!isset($is_editing)) $is_editing = false;?>
<td><?php echo $p->pk_parameter_id; ?></td>
<td title="<?php echo $p->name; ?>"><?php echo $p->descr; ?></td>
<td><?php
if ($is_editing) echo '<input value="'.$p->value.'" onchange="updateParameter('.$p->pk_parameter_id.', this.value);" />';
else echo $p->value;
?>
</td>

