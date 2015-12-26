
<h1>오류 페이지</h1>

<?=$model->get('exception')->getMessage();?><br />
<a href="/">이곳</a>을 눌러 메인 홈으로 이동하세요.

<? if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1'): ?>
	<div id="__divButton" style="padding-top:30px;text-align:center;">
		<a href="#" onclick="document.getElementById('__divTrace').style.display='block';document.getElementById('__divButton').style.display='none';">오류 내역 자세히 보기(* Local Server에서만 표시됩니다)</a>
	</div>
	<div id="__divTrace" style="display:none;padding-top:30px;">
		<? foreach ($model->get('exception')->getTrace() as $idx=>$trace): ?>
			<table>
				<thead>
					<tr>
						<th colspan="2"><?=$trace['file']?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>line</th>
						<td><?=$trace['line']?></td>
					</tr>
					<? if (isset($trace['function'])): ?>
						<tr>
							<th>function</th>
							<td><?=$trace['function']?></td>
						</tr>
					<? endif; ?>
					<? if (isset($trace['class'])): ?>
						<tr>
							<th>class</th>
							<td><?=$trace['class']?></td>
						</tr>
					<? endif; ?>
					<? if (isset($trace['type'])): ?>
						<tr>
							<th>type</th>
							<td><?=$trace['type']?></td>
						</tr>
					<? endif; ?>
					<? if (isset($trace['args'])): ?>
						<tr>
							<th>args</th>
							<td><xmp><?var_dump($trace['args'])?></xmp></td>
						</tr>
					<? endif; ?>
				</tbody>
			</table>
		<? endforeach; ?>
	</div>
<? endif; ?>
