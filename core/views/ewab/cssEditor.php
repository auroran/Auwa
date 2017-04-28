				<div id="cssEditor" unselectable="on">
					<pre id="ElmtActive">Body</pre>
					<button class="btn fa fa-remove" data-action="remove" data-title=" Supprimer"></button>
					<button class="btn fa fa-reply" data-action="unwrap" data-title=" Fusionner avec le parent / Extraire la sélection"></button>
					<button class="btn fa fa-level-up" data-action="parent" data-title=" Sélectionner l'élément parent" style=""></button>
					
					<!--div id="ui_css_gen" class="cssEditorUI">
						<p><strong><?php echo $transl['cssEditor']['general']['head'];?></strong>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['general']['width'];?></span>
							<span><input type="text" data="width" role="value"></span>
							<span><button role="validate" class="btn"data="width"></button></span>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['general']['height'];?></span>
							<span><input type="text" data="height" role="value"></span>
							<span><button role="validate" class="btn" data="height"></button></span>
						</p>

						<p class="separator"></p>
						<p>
							<span><?php echo $transl['cssEditor']['general']['display'];?></span>
							<span><select data="display" role="value">
								<option value=""><?php echo $transl	['default'];?></option>
								<option value="inline"><?php echo $transl['cssEditor']['general']['displayMenu']['inline'];?></option>
								<option value="inline-block"><?php echo $transl['cssEditor']['general']['displayMenu']['inlineBlock'];?></option>
								<option value="block"><?php echo $transl['cssEditor']['general']['displayMenu']['block'];?></option>
								<option value="table"><?php echo $transl['cssEditor']['general']['displayMenu']['table'];?></option>
								<option value="table-row"><?php echo $transl['cssEditor']['general']['displayMenu']['row'];?></option>
								<option value="table-cell"><?php echo $transl['cssEditor']['general']['displayMenu']['cell'];?>/option>
							</select> </span>
							<span><button role="validate" class="btn" data="display"></button></span>
						</p>
						<p class="listAttribute">
							<span><?php echo $transl['cssEditor']['general']['list'];?></span>
							<span><select data="list-style-type" role="value">
								<option value=""><?php echo $transl['default'];?></option>
								<option value="none"><?php echo $transl['none'];?></option>
								<option value="circle" data-tag="ul"><?php echo $transl['cssEditor']['general']['listMenu']['circle'];?></option>
								<option value="disc" data-tag="ul"><?php echo $transl['cssEditor']['general']['listMenu']['disc'];?></option>
								<option value="square" data-tag="ul"><?php echo $transl['cssEditor']['general']['listMenu']['square'];?></option>
								
								<option value="decimal" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['decimal'];?></option>
								<option value="decimal-leading-zero" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['decimalLeadingZero'];?></option>
								<option value="lower-roman" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['lowerRoman'];?></option>
								<option value="upper-roman" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['upperRoman'];?></option>
								<option value="lower-greek" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['greek'];?></option>
								<option value="lower-alpha" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['lowerAlpha'];?></option>
								<option value="upper-alpha" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['upperAlpha'];?></option>
								<option value="hebrew" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['hebrew'];?></option>
								<option value="armenian" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['armenian'];?></option>
								<option value="georgian" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['georgian'];?></option>
								<option value="cjk-ideographic" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['cjkIdeographic'];?></option>
								<option value="hiragana" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['hiragana'];?></option>
								<option value="hiragana-iroha" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['hiraganaIroha'];?></option>
								<option value="katakana" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['katakana'];?></option>
								<option value="katakana-iroha" data-tag="ol"><?php echo $transl['cssEditor']['general']['listMenu']['katakanaIroha'];?></option>
							</select>
							</span>
							<span class="help"><button role="validate" class="btn" data="list-style-type"></button></span>
						</p>
						<p class="separator"></p>
						<p>
							<span><?php echo $transl['cssEditor']['general']['float'];?></span>
							<span><select data="float" role="value">
								<option value=""><?php echo $transl['none'];?></option>
								<option value="left"><?php echo $transl['cssEditor']['left'];?></option>
								<option value="right"><?php echo $transl['cssEditor']['right'];?></option>
							</select> </span>
							<span><button role="validate" class="btn" data="float"></button></span>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['general']['left'];?></span>
							<span><input type="text" data="left" role="value"></span>
							<span><button role="validate" class="btn" data="left"></button></span>
						</p>
						<p style="display:none">
							<span><?php echo $transl['cssEditor']['general']['right'];?></span>
							<span><input type="text" data="right" role="value"></span>
							<span><button role="validate" class="btn" data="right"></button></span>
						</p>
						<p class="separator"></p>
						<p>
							<span><?php echo $transl['cssEditor']['general']['top'];?></span>
							<span><input type="text" data="top" role="value"></span>
							<span><button role="validate" class="btn" data="top"></button>
						</p>
						<p style="display:none">
							<span><?php echo $transl['cssEditor']['general']['bottom'];?></span>
							<span><input type="text" data="bottom" role="value"></span>
							<span><button role="validate" class="btn" data="bottom"></button></span>
						</p>
					</div-->
					<!-- Fond -->
					<!--div id="ui_css_bg" class="cssEditorUI">
						<p><strong><?php echo $transl['cssEditor']['background']['head'];?></strong>
						</p>
						<p>
							<span><i class="glyphicon glyphicon-picture"></i></span>
							<span><input type="text" data="background-picture" role="value"></span>
							<span class="help"><button class="btn glyphicon" data-tool="img">?</button></span>
							<span><button role="validate" class="btn" data="background-picture"></button></span>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['background']['color'];?></span>
							<span><input type="text" data="background-color" role="value"></span>
							<span class="help"><button class="btn glyphicon" data-tool="color">?</button></span>
							<span><button role="validate" class="btn" data="background-color"></button></span>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['background']['size'];?></span>
							<span><input type="text" data="background-size" role="value"></span>
							<span><button role="validate" class="btn" data="background-size"></button></span>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['background']['position'];?></span>
							<span><input type="text" data="background-position" role="value"></span>
							<span><button role="validate" class="btn" data="background-position"></button></span>
						</p>

						<p>
							<span><?php echo $transl['cssEditor']['background']['repeat'];?></span>
							<span><select data="background-picture" role="value">
								<option><?php echo $transl['cssEditor']['background']['repeatMenu']['enable'];?></option>
								<option value="repeat-x"><?php echo $transl['cssEditor']['background']['repeatMenu']['enableX'];?></option>
								<option value="repeat-y"><?php echo $transl['cssEditor']['background']['repeatMenu']['enableY'];?></option>
								<option value="no-repeat"><?php echo $transl['cssEditor']['background']['repeatMenu']['disable'];?></option>
							</select> </span>
							<span><button role="validate" class="btn" data="background-repeat"></button></span>
						</p>
					</div-->
					<!-- Textes -->
					<div id="ui_css_txt" class="cssEditorUI">
						<p><strong><?php echo $transl['cssEditor']['content']['head'];?></strong>
						</p>
						<p>
							<span class=""><?php echo $transl['cssEditor']['content']['indentation'];?></span>
							<span><input type="text" data="text-indent" role="value"></span>
							<span class="help"><button role="validate" class="btn" data="text-indent"></button></span>
						</p>
						<p>
							<span class=""><?php echo $transl['cssEditor']['content']['color'];?></span>
							<span><input type="text" data="color" role="value"></span>
							<span class="help"><button class="btn glyphicon" data-tool="color">?</button></span>
							<span><button role="validate" class="btn" data="color"></button></span>
						</p>
						<p>
							<span class=""><?php echo $transl['cssEditor']['content']['size'];?></span>
							<span><input type="text" data="font-size" role="value"></span>
							<span><button role="validate" class="btn" data="font-size"></button></span>
						</p>
						<p>
							<span class=""><?php echo $transl['cssEditor']['content']['lineHeight'];?></span>
							<span><input type="text" data="line-weight" role="value"></span>
							<span><button role="validate" class="btn" data="line-weight"></button></span>
						</p>
						<p>
							<span class=""><?php echo $transl['cssEditor']['content']['style'];?></span>
							<span>
								<input type="hidden" data="font-style" value="" role="value">
								<input type="radio" value="inherit" id="text-italic-inherit" name="font-style">
								<label for="text-italic-inherit" class="btn btn-default fa fa-share"></label>
								<input type="radio" value="normal" id="text-normal" name="font-style">
								<label for="text-normal" class="btn btn-default fa fa-ban"></label>
								<input type="radio" value="italic" id="text-italic" name="font-style">
								<label for="text-italic" class="btn btn-default fa fa-italic"></label>
							</span>
							<span><button class="btn btn-radio" role="validate" class="btn"data="font-style"></button></span>
						</p>
						<p>
							<span class=""><?php echo $transl['cssEditor']['content']['weight'];?></span> 
							<span class="l">
								<input type="hidden" data="font-weight" value="" role="value">
								<input type="radio" value="inherit" id="text-bold-inherit" name="font-weight">
								<label for="text-bold-inherit" class="btn btn-default fa fa-share"></label>
								<input type="radio" value="normal" id="text-nobold" name="font-weight">
								<label for="text-nobold" class="btn btn-default fa fa-ban"></label>
								<input type="radio" value="bold" id="text-bold" name="font-weight">
								<label for="text-bold" class="btn btn-default fa fa-bold"></label>
							</span>
							<span><button class="btn btn-radio" role="validate" class="btn"data="font-weight"></button></span>
						</p>
						<p>
							<span class=""><?php echo $transl['cssEditor']['content']['textAlign'];?></span>
							<span class="l">
								<input type="hidden" data="text-align" value="" role="value">
								<input type="radio" name="text-align" value="left" id="text-align-left">
								<label for="text-align-left" class="btn btn-default fa fa-align-left"></label>
								<input type="radio" name="text-align" value="center" id="text-align-center">
								<label for="text-align-center" class="btn btn-default fa fa-align-center"></label>
								<input type="radio" name="text-align" value="right" id="text-align-right">
								<label for="text-align-right" class="btn btn-default fa fa-align-right"></label>
								<input type="radio" name="text-align" value="justify" id="text-align-justify">
								<label for="text-align-justify" class="btn btn-default fa fa-align-justify"></label>
								<input type="radio" name="text-align" value="inherit" id="text-align-auto">
								<label for="text-align-auto" class="btn btn-default fa fa-share"></label>
							</span>
							<span><button class="btn btn-radio" role="validate" class="btn"data="text-align"></button></span>
						</p>
						<p class="tableCellAttribute">
							<span class=""><?php echo $transl['cssEditor']['content']['verticalAlign'];?></span>
							<span class="l">
								<input type="hidden" data="vertical-align" value="" role="value">
								<input type="radio" name="vertical-align" value="top" id="vertical-align-top">
								<label for="vertical-align-top" class="btn btn-default">Haut</label>
								<input type="radio" name="vertical-align" value="middle" id="vertical-align-middle">
								<label for="vertical-align-middle" class="btn btn-default">Milieu</label>
								<input type="radio" name="vertical-align" value="bottom" id="vertical-align-bottom">
								<label for="vertical-align-bottom" class="btn btn-default">Bas</label>
								<input type="radio" name="vertical-align" value="inherit" id="vertical-align-auto">
								<label for="vertical-align-auto" class="btn btn-default fa fa-share"></label>
							</span>
							<span><button class="btn btn-radio" role="validate" class="btn"data="vertical-align"></button></span>
						</p>

					</div>
					<!-- Marges -->
					<!--div id="ui_css_mg" class="cssEditorUI">
						<p>
							<span><?php echo $transl['cssEditor']['margin']['boxSizing'];?></span>
							<span><select type="text" data="box-sizing" role="value">
								<option value=""><?php echo $transl['default'];?></option>
								<option value="padding-box"><?php echo $transl['cssEditor']['margin']['boxSizingMenu']['paddingBox'];?></option>
								<option value="border-box"><?php echo $transl['cssEditor']['margin']['boxSizingMenu']['borderBox'];?></option>
							</select></span>
							<span><button role="validate" class="btn" data="box-sizing"></button></span>
						</p>
						<p>
							<strong><?php echo $transl['cssEditor']['margin']['padding'];?> &nbsp;&nbsp;
								<i class="cplx" style="font-size:.8em" data-attr="padding"  data-part="4">
									<input type="checkbox" value="false" id="editor-ui-unifyPadding"><?php echo $transl['cssEditor']['margin']['merge'];?>
								</i>
							</strong>
						</p>
						<p class="hiddenAttr unitedPadding">
							<span><?php echo $transl['cssEditor']['value'];?></span>
							<span><input type="text" data="padding" role="value"></span>
							<span><button role="validate" class="btn" data="padding"></button></span>
						</p>
						<p class="splitedPadding">
							<span><?php echo $transl['cssEditor']['left'];?></span>
							<span><input type="text" data="padding-left" data-part="0" data-attr="padding"  role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn"data="padding-left"></button></span>
						</p>
						<p class="splitedPadding">
							<span><?php echo $transl['cssEditor']['right'];?></span>
							<span><input type="text" data="padding-right" data-part="1" data-attr="padding" role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn"data="padding-right"></button></span>
						</p>
						<p class="splitedPadding">
							<span><?php echo $transl['cssEditor']['top'];?></span>
							<span><input type="text" data="padding-top" data-part="2" data-attr="padding"  role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn"data="padding-top"></button></span>
						</p>
						<p class="splitedPadding">
							<span><?php echo $transl['cssEditor']['bottom'];?></span>
							<span><input type="text" data="padding-bottom" data-part="3" data-attr="padding"  role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn"data="padding-bottom"></button></span>
						</p>

						<p>
							<strong><?php echo $transl['cssEditor']['margin']['margin'];?> &nbsp;&nbsp;
								<i class="cplx" style="font-size:.8em" data-attr="margin" data-part="4">
									<input type="checkbox" value="false" id="editor-ui-unifyMargin"><?php echo $transl['cssEditor']['margin']['merge'];?>
								</i>
							</strong>
						</p>
						<p class="hiddenAttr unitedMargin">
							<span><?php echo $transl['cssEditor']['value'];?></span>
							<span><input type="text" data="margin" role="value"></span>
							<span><button role="validate" class="btn" data="margin"></button></span>
						</p>
						<p class="splitedMargin">
							<span><?php echo $transl['cssEditor']['left'];?></span>
							<span><input type="text" data="margin-left" data-part="0" data-attr="margin"  role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn" data="margin-left"></button></span>
						</p>
						<p class="splitedMargin">
							<span><?php echo $transl['cssEditor']['right'];?></span>
							<span><input type="text" data="margin-right" data-part="1" data-attr="margin"  role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn" data="margin-right"></button></span>
						</p>
						<p class="splitedMargin">
							<span><?php echo $transl['cssEditor']['top'];?></span>
							<span><input type="text" data="margin-top" data-part="2" data-attr="margin"  role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn" data="margin-top"></button></span>
						</p>
						<p class="splitedMargin">
							<span><?php echo $transl['cssEditor']['bottom'];?></span>
							<span><input type="text" data="margin-bottom" data-part="3" data-attr="margin"  role="value"></span>
							<span><button class="btn cplx" role="validate" class="btn" data="margin-bottom"></button></span>
						</p>
						
					</div-->
					<!-- Borders -->
					<!--div id="ui_css_bo" class="cssEditorUI">
						<p><strong><?php echo $transl['cssEditor']['border']['head'];?></strong>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['border']['width'];?></span>
							<span><input type="text" data="border-width" role="value"></span>
							<span><button role="validate" class="btn" data="border-width"></button></span>
						</p>
						<p class="tableAttribute">
							<span><?php echo $transl['cssEditor']['border']['borderCell'];?></span>
							<span>
								<input type="hidden" data="border-collapse" value="" role="value">
								<input type="radio" name="border-collapse" value="separate" id="border-collapse-separate">
								<label for="border-collapse-separate" class="btn btn-default ">Séparées</label>
								<input type="radio" name="border-collapse" value="collapse" id="border-collapse-collapse">
								<label for="border-collapse-collapse" class="btn btn-default ">Collées</label>
								<input type="radio" name="border-collapse" value="inherit" id="border-collapse-auto">
								<label for="border-collapse-auto" class="btn btn-default fa fa-share"></label>
							</span>
							<span><button class="btn btn-radio" role="validate" class="btn"data="border-collapse"></button></span>
						</p>

						<p>
							<span><?php echo $transl['cssEditor']['border']['color'];?></span>
							<span><input type="text" data="border-color" role="value"></span>
							<span><button class="btn glyphicon glyphicon">?</button></span>
							<span><button role="validate" class="btn" data="border-color"></button></span>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['border']['style'];?></span>
							<span><select type="text" data="border-style" role="value">
								<option value=""><?php echo $transl['default'];?></option>
								<option value="solid"><?php echo $transl['cssEditor']['border']['styleMenu']['solid'];?></option>
								<option value="dashed"><?php echo $transl['cssEditor']['border']['styleMenu']['dashed'];?></option>
								<option value="dotted"><?php echo $transl['cssEditor']['border']['styleMenu']['dotted'];?></option>
							</select></span>
							<span><button role="validate" class="btn" data="border-style"></button></span>
						</p>
						<p>
							<span><?php echo $transl['cssEditor']['border']['radius'];?></span>
							<span><input type="text" data="border-radius" role="value"></span>
							<span><button role="validate" class="btn" data="border-radius"></button></span>
						</p>
						
					</div-->
				</div>
