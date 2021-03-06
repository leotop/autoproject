<h1>Настройка платежной системы "Альфа Банк"</h1>
<form action="" method="POST">
<input type="hidden" name="save" value="1">
<table width="500px">
<?php if (isset($data) && count($data)>0){?>
<?php foreach ($data as $dd){?>
<tr>
	<td><b><?=$dd['name']?></b></td>
	<td>
		<?php if ($dd['type'] == 'radio'){?>
			<input type="<?=$dd['type']?>" name="code[<?=$dd['id']?>]" value="1" <?=($dd['value']==1)?'checked':''?>> Да
			<input type="<?=$dd['type']?>" name="code[<?=$dd['id']?>]" value="0" <?=($dd['value']==0)?'checked':''?>> Нет
		<?php }else{?>
			<input type="<?=$dd['type']?>" name="code[<?=$dd['id']?>]" value="<?=$dd['value']?>" class="iput">
		<?php }?>
	</td>
</tr>
<?php }?>
<?php }?>
<tr>
	<td></td>
	<td><input type="submit" value="Сохранить" class="btn btn-green"></td>
</tr>
<tr>
	<td></td>
	<td><sup>Неправильная настройка может привести к ошибкам работы сайта. В случае ошибок просим писать запросы в техподдержку в разделе "billing" блока "Настройка / Управление"</sup></td>
</tr>
</table>
</form>

<h1 style="margin-top:40px;">Платежи "Альфа Банк"</h1>
<table class="list">
<thead>
<tr>
	<th>ID</th>
	<th>Название</th>
	<th>Сумма операции</th>
	<th>Номер счета (Транзакция)</th>
	<th>Комментарий</th>
	<th>Статус</th>
	<th>Дата создания счета</th>
	<th>Дата проверки статуса</th>
	<th><img title="<?=$translates['admin.main.delete']?>" border="0" src="/staffcp/media/images/trash_16.png"/></th>
</tr>
</thead>
<tbody>
<?php if (isset($bills) && count($bills)>0){?>
<?php foreach ($bills as $dd){?>
<tr>
	<td><?=$dd['id']?></td>
	<td><?=$dd['name']?></td>
	<td><?=$dd['osum']?></td>
	<td><?=$dd['orderid_bill']?></td>
	<td><?=$dd['comment']?></td>
	<td><?=$dd['status']?></td>
	<td><?=date("d.m.Y H:i:s",$dd['create_dt'])?></td>
	<td><?=($dd['check_dt'])?date("d.m.Y H:i:s",$dd['check_dt']):''?></td>
	<td width="25px;" style="text-align:right;"><a href="?delete=<?=$dd['id']?>" onclick="return confirm('<?=$translates['admin.main.confirm']?>');"><img title="<?=$translates['admin.main.delete']?>" border="0" src="/staffcp/media/images/trash_16.png"/></a></td>
</tr>
<?php }?>
<?php }?>
</tbody>
</table>