<?php
/**
	Copyright (C) 2015 Ali BELLAMINE

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
**/

	/**
		28/07/2015 - Ali BELLAMINE
		admin/bug.php - Page permettant de visualiser le signalement des bugs
	**/
	require '../core/main.php';
	require '../core/header.php';
	
	/**
		Routage : variable $action
	**/
	
	$allowedAction = array('list', 'view');
	if (isset($_GET['action']) && in_array($_GET['action'], $allowedAction))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'list';
	}
	
	/**
		1. Récupération des données
	**/
	
		if ($action == 'list')
		{
			// Liste des années pour lesquels il y a des bugs à récupérer
			$listeAnnee = array();
			$sql = 'SELECT DISTINCT EXTRACT(year FROM bugDate) Annee FROM `bug` ORDER BY bugDate DESC';
			$res = $db -> query($sql);
			while ($res_f = $res -> fetch())
			{
				$listeAnnee[] = $res_f['Annee'];
			}
			
			// On récupère l'année dont on souhaite extraire la liste des bugs : dans une variable GET, si absente on récupère l'année la plus récente
			if (isset($_GET['annee']) && in_array($_GET['annee'], $listeAnnee))
			{
				$sqlAnnee = $_GET['annee'];
			}
			else if (count(array_keys($listeAnnee)) > 0)
			{
				$sqlAnnee = $listeAnnee[array_keys($listeAnnee)[0]];
			}
			else
			{
				$sqlAnnee = FALSE;
			}
			
			// Liste des bugs
			$bugList = array();
			if ($sqlAnnee)
			{
				$bugList = array();
				$sql = 'SELECT bugId id, bugDescription description, bugDate date, bugState state FROM bug WHERE EXTRACT(year FROM bugDate) = ? ORDER BY bugState ASC, bugDate DESC';
				$res = $db -> prepare($sql);
				$res -> execute(array($sqlAnnee));
				while ($res_f = $res -> fetch())
				{
					$bugList[$res_f['id']]['id'] = $res_f['id'];
					$bugList[$res_f['id']]['date'] = $res_f['date'];
					$bugList[$res_f['id']]['description'] = $res_f['description'];
					$bugList[$res_f['id']]['state'] = $res_f['state'];
				}
			}
		}
		else if ($action == 'view' && isset($_GET['id']) && is_numeric($_GET['id']))
		{
			$bugData = array();
			$sql = 'SELECT bugId id, bugDescription description, bugServerData serverData, bugSessionVariable sessionVariable, bugDate date, bugState state FROM bug WHERE bugId = ? LIMIT 1';
			$res = $db -> prepare($sql);
			$res -> execute(array($_GET['id']));
			if ($res_f = $res -> fetch())
			{
				$bugData['id'] = $res_f['id'];
				$bugData['date'] = $res_f['date'];
				$bugData['server'] = unserialize($res_f['serverData']);
				$bugData['session'] = unserialize($res_f['sessionVariable']);
				$bugData['description'] = $res_f['description'];
				$bugData['state'] = $res_f['state'];
			}
			else
			{
				header('Location: '.ROOT.CURRENT_FILE);
			}
		}
		
	/**
		2. Validation des bugs
	**/
	
	if (isset($_GET['check']) || isset($_GET['uncheck']) && isset($_GET['id']) && is_numeric($_GET['id']))
	{
		if (isset($_GET['check']) && !isset($_GET['uncheck']))
		{
			$sql = 'UPDATE bug SET bugState = 1 WHERE bugId = ?';
		}
		else if (!isset($_GET['check']) && isset($_GET['uncheck']))
		{
			$sql = 'UPDATE bug SET bugState = 0 WHERE bugId = ?';
		}
		
		$res = $db -> prepare($sql);
		$res -> execute(array($_GET['id']));
		header('Location: '.ROOT.CURRENT_FILE);
	}

	/**
		3. Affichage des données
	**/
	
	?>			
		<h1><?php echo LANG_ADMIN_BUG_MANAGER_TITLES; ?></h1>

		<?php
		if ($action == 'list')
		{
		?>
		<!-- Liste des année disponibles -->
		<div style = "margin: 10px;">
			<form method = "GET">
				<select name = "annee">
					<option value = ""><?php echo LANG_ADMIN_BUG_MANAGER_SELECT_YEAR; ?></option>
					<?php
						foreach ($listeAnnee AS $annee)
						{
							?>
							<option <?php if (isset($sqlAnnee) && $sqlAnnee == $annee) { echo 'selected'; } ?> value = "<?php echo $annee; ?>"><?php echo $annee; ?></option>
							<?php
						}
					?>
				</select>
			</form>
		</div>

		<table style = "margin-top: 10px;">
			<tr class = "headTR">
				<th><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_BUGDATE; ?></th>
				<th><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_DESCRIPTION; ?></th>
				<th><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT; ?></th>
				<th></th>
			</tr>
			<?php
				if (isset($bugList) & count ($bugList) > 0)
				{
					foreach ($bugList AS $bugAnnee => $bug)
					{
						?>
						<tr class = "bodyTR">
							<td><? echo date('d/m/Y', DatetimeToTimestamp($bug['date'])); ?></td>
							<td><? echo $bug['description']; ?></td>
							<td style = "color: <?php if ($bug['state'] == 1) { echo 'green'; } else { echo 'red'; } ?>;"><? echo constant('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT_VALUE_'.$bug['state']); ?></td>
							<td>
								<a href = "<?php echo ROOT.CURRENT_FILE.'?action=view&id='.$bug['id']; ?>"><i class="fa fa-bug"></i></a> <!-- Bouton pour voir le détail du bug  -->
								<!-- Bouton pour checker ou dechecker un bug -->
								<?php
									if ($bug['state'] == 0)
									{
										?>
											<a href = "<?php echo ROOT.CURRENT_FILE.'?action=list&check=1&id='.$bug['id']; ?>"><i class="fa fa-check-square"></i></a> 
										<?php
									}
									else
									{
									?>
											<a href = "<?php echo ROOT.CURRENT_FILE.'?action=list&uncheck=1&id='.$bug['id']; ?>"><i class="fa fa-minus-square"></i></a> 
									<?php
									}
									?>
							</td>
						</tr>
						<?php
					}
				}
				else
				{
					?>
					<td colspan = "6"><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_NOBUGREGISTERED; ?></td>
					<?php
				}
			?>
		</table>
	<?php
		}
		else if ($action == 'view')
		{
			?>
				<a href = "<?php echo ROOT.CURRENT_FILE; ?>"><i class="fa fa-arrow-left"></i></a>

				<h2><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_BUGDATE; ?></h2>
				<p><?php echo $bugData['date']; ?></p>

				<h2><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_DESCRIPTION; ?></h2>
				<p><?php echo $bugData['description']; ?></p>

				<h2><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_SESSIONVARIABLE; ?></h2>
				<table>
					<tr class = "headTR">
					<?php
						foreach ($bugData['session'] AS $key => $value)
						{
							?>
								<th><?php echo $key; ?></th>
							<?php
						}
					?>
					</tr>
					<tr class = "bodyTR">
					<?php
						foreach ($bugData['session'] AS $key => $value)
						{
							?>
								<th>
									<?php
										if (is_array($value))
										{
											var_dump($value);
										}
										else
										{
											echo $value;
										}
									?>
								</th>
							<?php
						}
					?>
					</tr>
				</table>

				<h2><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_SERVERVARIABLE; ?></h2>
				<table style = "display: block; overflow: auto;">
					<tr class = "headTR">
					<?php
						foreach ($bugData['server'] AS $key => $value)
						{
							?>
								<th><?php echo $key; ?></th>
							<?php
						}
					?>
					</tr>
					<tr class = "bodyTR">
					<?php
						foreach ($bugData['server'] AS $key => $value)
						{
							?>
								<th>
									<?php
										if (is_array($value))
										{
											var_dump($value);
										}
										else
										{
											echo $value;
										}
									?>
								</th>
							<?php
						}
					?>
					</tr>
				</table>
				
				<h2><?php echo LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT; ?></h2>
				<p style = "color: <?php if ($bugData['state'] == 1) { echo "green"; } else { echo 'red'; } ?>;"><?php echo constant('LANG_ADMIN_BUG_MANAGER_TABLE_TITLE_STATUT_VALUE_'.$bugData['state']); ?></p>
			<?php
		}
	
	require '../core/footer.php';
?>