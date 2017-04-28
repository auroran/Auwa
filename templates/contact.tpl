<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="generator" content="Auwa ({$AuwaVersion})">

</head>
<body>
	<h1> Vous avez reçu un message sur le site {$website} {if isset($current_page)} (page {$current_page}){/if}.</h1>
	
	<section>
		<h2> La personne qui vous a contactée :</h2>
		<table border="0">
			<tr>
				<td>Identité : </td>
				<td>{$mailContactName}</td>
			</tr>
			<tr>
				<td>E-Mail : </td>
				<td>{$mailContactMail}</td>
			</tr>
		</table>
	</section>
	
	<section>
		<h2> Son message:</h2>
		<p>
		{$mailContent}
		</p>
	</section>
	
</body>


</html>