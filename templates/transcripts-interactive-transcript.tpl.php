<div>
	<div class='transcript-player'>
		<table>
			<tr>
				<td class='video-wrapper' style='padding-right: 20px;'>
					<?php print $video_tag; ?>
					<?php print $tier_selector; ?>
				</td>
                                <td class='transcript-wrapper'>
					<?php print render($transcript_controls); ?>
                                        <ul class='nav nav-tabs' role='tablist'>
                                                <li class='active'><a href='#transcript-<?php print $trid; ?>' role='tab' data-toggle='tab'>Transcript</a></li>
                                                <li><a href='#hits-<?php print $trid; ?>' role='tab' data-toggle='tab'>Search</a></li>
                                        </ul>
                                        <div class='transcript-content tab-content'>
                                                <div class='tab-pane active' id='transcript-<?php print $trid; ?>'>
                                                        <?php print render($transcript); ?>
                                                </div>
                                                <div class='tab-pane' id='hits-<?php print $trid; ?>'>
                                                        <?php print render($hits); ?>
                                                </div>
                                        </div>
                                </td>
			</tr>
		</table>
	</div>
</div>
