<script>
$(document).ready(function() {
	$('.default-value').each(function() {
		var default_value = this.value;
		$(this).focus(function() {
			if(this.value == default_value) {
				this.value = '';
				$(this).css('color', '#333');
			}
		});
		$(this).blur(function() {
			if(this.value == '') {
				$(this).css('color', '#999');
				this.value = default_value;
			}
		});
	});

	$('#txtUniverseCount').on('focus',function() {
		$(this).select();
	});

	$('#tblUniverses').on('mousedown', 'tr', function(event,ui){
		$('#tblUniverses tr').removeClass('selectedEntry');
		$(this).addClass('selectedEntry');
		var items = $('#tblUniverses tr');
		UniverseSelected  = items.index(this);
	});

	$('#frmUniverses').submit(function(event) {
			 event.preventDefault();
			 var success = validateUniverseData();
			 if(success == true)
			 {
				 dataString = $("#frmUniverses").serializeArray();

				 enabled = {};
				 enabled.name = "enabled";

				 if ($("#E131Enabled").is(':checked'))
				 	enabled.value = 1;
				 else
				 	enabled.value = 0;

				 dataString.push(enabled);

				 $.ajax({
						type: "post",
						url: "fppjson.php",
						dataType:"text",
						data: dataString,
						success: function (response) {
								getUniverses('FALSE', 0);
								$.jGrowl("E1.31 Universes Saved");
								SetRestartFlag();
						}
				}).fail( function() {
					DialogError("Save E1.31 Universes", "Save Failed");
				});
				return false;
			 }
			 else
			 {
			   DialogError("Save E1.31 Universes", "Validation Failed");
			 }
	});

	InitializeUniverses();
	getUniverses('TRUE', 0);
});

<?
function PopulateInterfaces()
{
	global $settings;

	$interfaces = explode("\n",trim(shell_exec("/sbin/ifconfig | cut -f1 -d' ' | grep -v ^$ | grep -v lo | grep -v usb | grep -v SoftAp | grep -v 'can.'")));
	$ifaceE131 = "";
	if (isset($settings['E131interface'])) {
		$ifaceE131 = $settings['E131interface'];
	}
	$found = 0;
	if ($ifaceE131 == "") {
		$ifaceE131 = "eth0";
	}
	foreach ($interfaces as $iface)
	{
		$iface = preg_replace("/:$/", "", $iface);
		$ifaceChecked = "";
		if ($iface == $ifaceE131) {
			$ifaceChecked = " selected";
			$found = 1;
		}
		echo "<option value='" . $iface . "'" . $ifaceChecked . ">" . $iface . "</option>";
	}
	if (!$found && ($ifaceE131 != "")) {
		echo "<option value='" . $ifaceE131 . "' selected>" . $ifaceE131 . "</option>";
	}
}
?>

</script>

<div id='tab-e131'>
	<div id='divE131'>
		<fieldset class="fs">
			<legend> E1.31 / ArtNet </legend>
			<div id='divE131Data'>

				<div style="overflow: hidden; padding: 10px;">
					<b>Enable E1.31 /ArtNet Output:</b> <? PrintSettingCheckbox("E1.31 / ArtNet Output", "E131Enabled", 1, 0, "1", "0"); ?><br><br>
					E1.31 / ArtNet Interface: <select id="selE131interfaces" onChange="SetE131interface();"><? PopulateInterfaces(); ?></select>
					<br><br>

					<div>
						<form>
							Universe Count: <input id="txtUniverseCount" class="default-value" type="text" value="Enter Universe Count" size="3" maxlength="3" /><input id="btnUniverseCount" onclick="SetUniverseCount(0);" type="button"  class="buttons" value="Set" />
						</form>
					</div>
					<form id="frmUniverses">
						<input name="command" type="hidden" value="setUniverses" />
						<input name="input" type="hidden" value="0" />
						<table>
							<tr>
								<td width = "70 px"><input id="btnSaveUniverses" class="buttons" type="submit" value = "Save" /></td>
								<td width = "70 px"><input id="btnCloneUniverses" class="buttons" type="button" value = "Clone" onClick="CloneUniverse();" /></td>
								<td width = "40 px">&nbsp;</td>
								<td width = "70 px"><input id="btnDeleteUniverses" class="buttons" type="button" value = "Delete" onClick="DeleteUniverse(0);" /></td>
							</tr>
						</table>

						<table id="tblUniverses" class='channelOutputTable'>
						</table>
					</form>
				</div>
			</div>
		</fieldset>
	</div>
</div>
