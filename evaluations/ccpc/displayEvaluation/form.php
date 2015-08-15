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
	Création du formulaire
**/

if (is_file(PLUGIN_PATH.'formulaire.xml'))
{
	if  ($form = simplexml_load_file(PLUGIN_PATH.'formulaire.xml'))
	{
		?>
		<!-- On crée la structure du formulaire -->
		<form method = "post" action = "<?php echo ROOT.'content/evaluation/view.php?id='.$evaluationData['register']['id']; ?>">
			<fieldset>
				<legend><?php echo LANG_FORM_CCPC_FILTER_SERVICE_TITLE; ?></legend>
				<select name = "service" style = "width: 100%">
					<?php
						foreach ($nonEvaluationData['data'] AS $nonEvaluation)
						{
							$service = getServiceInfo($nonEvaluation['service']);
						?>
							<option value = "<?php echo $service['id']; ?>"><?php echo $service['FullName']; ?></option>
						<?php
						}
					?>
				</select>
			</fieldset>
		<?php
		foreach ($form -> categorie AS $categorie)
		{
			/**
				On crée la catégorie
			**/
			?>
			
			<fieldset>
				<legend><?php if (isset($categorie['legend'])) { echo constant($categorie['legend']); } ?></legend>
				<?php
					// On crée les input de la catégorie
					foreach($categorie -> input AS $input)
					{
						/**
							On crée l'input du formulaire
						**/
						
						if (isset($input['label']))
						{
							/*
								On affiche le label
							*/
							?>
							<label class = "titleLabel" for "<?php echo $input['name']; ?>">
								<?php echo constant($input['label']); ?>
							</label>
							<?php
						}
						
						if ($input['type'] == 'select')
						{
							?>
							<select id = "<?php echo $input['name']; ?>" name = "<?php echo $input['name']; ?>">-->
								<?php
									foreach($input -> option AS $option)
									{
										?>
										<option value = "<?php echo $option['value']; ?>" <?php if (isset($_POST[(string) $input['name']]) && $_POST[(string) $input['name']] == $option['value']) { echo 'selected'; } ?>>
											<?php echo constant($option['text']); ?>
										</option>
										<?php
									}
								?>
							</select>
							<?php
						}
						else if ($input['type'] == 'checkbox')
						{
							foreach($input -> checkbox AS $checkbox)
							{
								?>
								<label for = "<?php echo $input['name'].'_'.$checkbox['value']; ?>"><input type="checkbox" name="<?php echo $input['name'].'[]'; ?>" id = "<?php echo $input['name'].'_'.$checkbox['value']; ?>" value="<?php echo $checkbox['value']; ?>" <?php if (isset($_POST[(string) $input['name']]) && in_array($checkbox['value'], $_POST[(string) $input['name']])) { echo 'checked'; } ?>>
									<?php echo constant($checkbox['text']); ?>
								</label>
								<?php
							}
						}
						else if ($input['type'] == 'radio')
						{
							foreach($input -> radio AS $radio)
							{
								?>
								<label for = "<?php echo $input['name'].'_'.$radio['value']; ?>"><input type="radio" name="<?php echo $input['name']; ?>" id = "<?php echo $input['name'].'_'.$radio['value']; ?>" value="<?php echo $radio['value']; ?>" <?php if (isset($_POST[(string) $input['name']]) && $radio['value'] == $_POST[(string) $input['name']]) { echo 'checked'; } ?>>
									<?php echo constant($radio['text']); ?>
								</label>
								<?php
							}
						}
						else if ($input['type'] == 'text')
						{
							foreach($input -> text AS $text)
								{
									?>
									<input type = "text" class = "<?php if (isset($text['fullwidth']) && $text['fullwidth'] == 1) { echo 'fullwidth'; } ?>" name = "<?php echo $text['name']; ?>" placeholder = "<?php if (isset($text['placeholder'])) { echo constant($text['placeholder']); } ?>" value = "<?php if (isset($_POST[(string) $text['name']])) { echo $_POST[(string) $text['name']]; } ?>" />
									<?php
								}
						}
						else if ($input['type'] == 'textarea')
						{
							?>
							<textarea class = "<?php if (isset($input['fullwidth']) && $input['fullwidth'] == 1) { echo 'fullwidth'; } ?>" name = "<?php echo $input['name']; ?>"><?php if (isset($_POST[(string) $input['name']])) { echo $_POST[(string) $input['name']]; } ?></textarea>
							<?php
						}
						?>
							<div class = "smallSeparator"></div>
						<?php
					}
				?>
			</fieldset>
			<?php
		}
		?>
		<!-- Fermeture du formulaire -->
		
		<input type = "submit" value = "<?php echo constant($form -> submit['value']); ?>" />
		</form>
		<?php
	}
}		
?>