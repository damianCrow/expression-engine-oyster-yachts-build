this["ChannelImages"] = this["ChannelImages"] || {};
this["ChannelImages"]["Templates"] = this["ChannelImages"]["Templates"] || {};

this["ChannelImages"]["Templates"]["editor_ci_modal"] = Handlebars.template({"1":function(container,depth0,helpers,partials,data) {
    var helper, alias1=container.escapeExpression;

  return "    <li><a href=\"#"
    + alias1(((helper = (helper = helpers.key || (data && data.key)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"key","hash":{},"data":data}) : helper)))
    + "\">"
    + alias1(container.lambda((depth0 != null ? depth0.field_label : depth0), depth0))
    + "</a></li>\n";
},"3":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3=container.escapeExpression;

  return alias3((helpers.setIndex || (depth0 && depth0.setIndex) || alias2).call(alias1,(data && data.key),{"name":"setIndex","hash":{},"data":data}))
    + "\n<div id=\""
    + alias3(((helper = (helper = helpers.key || (data && data.key)) != null ? helper : alias2),(typeof helper === "function" ? helper.call(alias1,{"name":"key","hash":{},"data":data}) : helper)))
    + "\" class=\"tabcontent\">\n\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.wimages : depth0),{"name":"if","hash":{},"fn":container.program(4, data, 0, blockParams, depths),"inverse":container.program(10, data, 0, blockParams, depths),"data":data})) != null ? stack1 : "")
    + "\n</div>\n";
},"4":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, alias1=depth0 != null ? depth0 : {};

  return "    <div class=\"imageholder\">\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.wimages : depth0),{"name":"each","hash":{},"fn":container.program(5, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "    </div>\n    <br clear=\"all\">\n\n    <div class=\"sizeholder\">\n        <ul>\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.sizes : depth0),{"name":"each","hash":{},"fn":container.program(7, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "        </ul>\n        <br clear=\"all\">\n    </div>\n";
},"5":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=container.lambda, alias2=container.escapeExpression;

  return "        <div class=\"CImage\">\n            <img src=\""
    + ((stack1 = alias1((depth0 != null ? depth0.big_img_url : depth0), depth0)) != null ? stack1 : "")
    + "\" title=\""
    + ((stack1 = alias1((depth0 != null ? depth0.title : depth0), depth0)) != null ? stack1 : "")
    + "\" alt=\""
    + ((stack1 = alias1((depth0 != null ? depth0.description : depth0), depth0)) != null ? stack1 : "")
    + "\" data-filename=\""
    + ((stack1 = alias1((depth0 != null ? depth0.filename : depth0), depth0)) != null ? stack1 : "")
    + "\" data-field_id=\""
    + alias2(alias1((depth0 != null ? depth0.field_id : depth0), depth0))
    + "\" data-index=\""
    + alias2(((helper = (helper = helpers.index || (data && data.index)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"index","hash":{},"data":data}) : helper)))
    + "\">\n        </div>\n";
},"7":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, alias1=container.lambda, alias2=container.escapeExpression;

  return "            <li><input name=\"size_"
    + alias2(alias1((depths[1] != null ? depths[1].parentindex : depths[1]), depth0))
    + "\" type=\"radio\" value=\""
    + alias2(alias1((depth0 != null ? depth0.name : depth0), depth0))
    + "\" "
    + ((stack1 = helpers["if"].call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.checked : depth0),{"name":"if","hash":{},"fn":container.program(8, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "> "
    + alias2(alias1((depth0 != null ? depth0.label : depth0), depth0))
    + "</li>\n";
},"8":function(container,depth0,helpers,partials,data) {
    return "checked";
},"10":function(container,depth0,helpers,partials,data) {
    return "    <p style=\"padding:10px\">No images have yet been uploaded.</p>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, alias1=depth0 != null ? depth0 : {};

  return "<section id=\"redactor-modal-paste_plain_text\" class=\"WCI_Images\">\n\n\n<ul class=\"tabs\">\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.fields : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "</ul>\n\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.fields : depth0),{"name":"each","hash":{},"fn":container.program(3, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n\n</section>\n\n<footer>\n    <button class=\"redactor_modal_btn redactor_btn_modal_close\">Cancel</button><!-- INLINE WHITESPACE DO NOT REMOVE\n --><button class=\"redactor_modal_btn redactor_modal_action_btn\">Insert</button>\n</footer>";
},"useData":true,"useDepths":true});

this["ChannelImages"]["Templates"]["mcp_batch_action_row"] = Handlebars.template({"1":function(container,depth0,helpers,partials,data) {
    return "action_loading";
},"3":function(container,depth0,helpers,partials,data) {
    return "    <strong class=\"action_done\">DONE</strong>\n";
},"5":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {};

  return "    ID: "
    + ((stack1 = ((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "&nbsp;&nbsp;\n\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.ajax_error : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n";
},"6":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "    <strong style=\"color:red\">(ERROR)</strong>&nbsp;&nbsp;\n    <span class=\"action_error\">\n        <span class=\"channel\"><strong>Channel:</strong> "
    + alias4(((helper = (helper = helpers.channel || (depth0 != null ? depth0.channel : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"channel","hash":{},"data":data}) : helper)))
    + "</span>&nbsp;&nbsp;\n        <span class=\"entry\"><strong>Entry:</strong> "
    + alias4(((helper = (helper = helpers.entry || (depth0 != null ? depth0.entry : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"entry","hash":{},"data":data}) : helper)))
    + "</span>&nbsp;&nbsp;\n        <span class=\"field\"><strong>Field:</strong> "
    + alias4(((helper = (helper = helpers.field || (depth0 != null ? depth0.field : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"field","hash":{},"data":data}) : helper)))
    + "</span>&nbsp;&nbsp;\n        <span class=\"field\"><strong>Image:</strong> "
    + alias4(container.lambda(((stack1 = (depth0 != null ? depth0.image : depth0)) != null ? stack1.title : stack1), depth0))
    + "</span>\n\n        <strong class=\"show_error\">SHOW ERROR</strong>&nbsp;&nbsp;\n\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.retry : depth0),{"name":"if","hash":{},"fn":container.program(7, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n        <script type=\"text/x-ci_debug\">\n        "
    + ((stack1 = ((helper = (helper = helpers.ajax_error || (depth0 != null ? depth0.ajax_error : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"ajax_error","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\n        </span>\n\n    </span>\n";
},"7":function(container,depth0,helpers,partials,data) {
    return "        (<span class=\"retry\">Retrying in 3 seconds..</span>)\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=depth0 != null ? depth0 : {};

  return "<td class=\"action_row "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.loading : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\">Action</td>\n<td>\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.action_done : depth0),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.program(5, data, 0),"data":data})) != null ? stack1 : "")
    + "</td>\n";
},"useData":true});

this["ChannelImages"]["Templates"]["mcp_regen_fieldsizes"] = Handlebars.template({"1":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var alias1=container.lambda, alias2=container.escapeExpression;

  return "      <label><input type=\"checkbox\" name=\"sizes["
    + alias2(alias1((depths[1] != null ? depths[1].field_id : depths[1]), depth0))
    + "][]\" value=\""
    + alias2(alias1(depth0, depth0))
    + "\" checked> "
    + alias2(alias1(depth0, depth0))
    + "</label> &nbsp;&nbsp;&nbsp;\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function";

  return "<tr>\n    <td>"
    + ((stack1 = ((helper = (helper = helpers.group || (depth0 != null ? depth0.group : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"group","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</td>\n  <td>"
    + ((stack1 = ((helper = (helper = helpers.field || (depth0 != null ? depth0.field : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"field","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</td>\n  <td>\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.sizes : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + " </td>\n</tr>";
},"useData":true,"useDepths":true});

this["ChannelImages"]["Templates"]["pbf_table_tr"] = Handlebars.template({"1":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function";

  return "<tr class=\"Image image-table "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.is_cover : depth0),{"name":"if","hash":{},"fn":container.program(2, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\" data-filename=\""
    + ((stack1 = ((helper = (helper = helpers.filename || (depth0 != null ? depth0.filename : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"filename","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_row_num : depth0),{"name":"if","hash":{},"fn":container.program(4, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_id : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_image : depth0),{"name":"if","hash":{},"fn":container.program(8, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_filename : depth0),{"name":"if","hash":{},"fn":container.program(10, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_title : depth0),{"name":"if","hash":{},"fn":container.program(12, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_url_title : depth0),{"name":"if","hash":{},"fn":container.program(14, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_desc : depth0),{"name":"if","hash":{},"fn":container.program(16, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_category : depth0),{"name":"if","hash":{},"fn":container.program(18, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_cifield_1 : depth0),{"name":"if","hash":{},"fn":container.program(20, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_cifield_2 : depth0),{"name":"if","hash":{},"fn":container.program(22, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_cifield_3 : depth0),{"name":"if","hash":{},"fn":container.program(24, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_cifield_4 : depth0),{"name":"if","hash":{},"fn":container.program(26, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_cifield_5 : depth0),{"name":"if","hash":{},"fn":container.program(28, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    <td>\n        "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_image_action : depth0),{"name":"if","hash":{},"fn":container.program(30, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n        <a href='javascript:void(0)' class='gIcon ImageMove'></a>\n        "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_cover : depth0),{"name":"if","hash":{},"fn":container.program(33, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n        "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_image_edit : depth0),{"name":"if","hash":{},"fn":container.program(38, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n        "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_image_replace : depth0),{"name":"if","hash":{},"fn":container.program(40, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n        <a href=\"#\" "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.is_linked : depth0),{"name":"if","hash":{},"fn":container.program(42, data, 0),"inverse":container.program(44, data, 0),"data":data})) != null ? stack1 : "")
    + "></a>\n        <textarea name=\""
    + ((stack1 = ((helper = (helper = helpers.field_name || (depth0 != null ? depth0.field_name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"field_name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "[images][][data]\" class=\"ImageData cihidden\">"
    + ((stack1 = ((helper = (helper = helpers.json_data || (depth0 != null ? depth0.json_data : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"json_data","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea>\n    </td>\n</tr>\n";
},"2":function(container,depth0,helpers,partials,data) {
    return "PrimaryImage";
},"4":function(container,depth0,helpers,partials,data) {
    return "<td class=\"num\"></td>";
},"6":function(container,depth0,helpers,partials,data) {
    var stack1, helper;

  return "<td>"
    + ((stack1 = ((helper = (helper = helpers.image_id || (depth0 != null ? depth0.image_id : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"image_id","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</td>";
},"8":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function";

  return "<td>\n        <a href='"
    + ((stack1 = ((helper = (helper = helpers.big_img_url || (depth0 != null ? depth0.big_img_url : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"big_img_url","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "' class='ImgUrl' rel='ChannelImagesGal' title='"
    + ((stack1 = ((helper = (helper = helpers.image_title || (depth0 != null ? depth0.image_title : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"image_title","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "'>\n            <img src=\""
    + ((stack1 = ((helper = (helper = helpers.small_img_url || (depth0 != null ? depth0.small_img_url : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"small_img_url","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\" width=\""
    + ((stack1 = ((helper = (helper = helpers.img_preview_size || (depth0 != null ? depth0.img_preview_size : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"img_preview_size","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\" alt=\""
    + ((stack1 = ((helper = (helper = helpers.image_title || (depth0 != null ? depth0.image_title : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"image_title","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n        </a></td>\n";
},"10":function(container,depth0,helpers,partials,data) {
    var stack1, helper;

  return "<td>"
    + ((stack1 = ((helper = (helper = helpers.filename || (depth0 != null ? depth0.filename : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"filename","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</td>";
},"12":function(container,depth0,helpers,partials,data) {
    var helper;

  return "<td class=\"ci-title\" data-field=\"title\"><input type=\"text\" value=\""
    + container.escapeExpression(((helper = (helper = helpers.image_title || (depth0 != null ? depth0.image_title : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"image_title","hash":{},"data":data}) : helper)))
    + "\" class=\"image_title\"></td>";
},"14":function(container,depth0,helpers,partials,data) {
    var stack1, helper;

  return "<td class=\"ci-url_title\" data-field=\"url_title\"><textarea>"
    + ((stack1 = ((helper = (helper = helpers.image_url_title || (depth0 != null ? depth0.image_url_title : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"image_url_title","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea></td>";
},"16":function(container,depth0,helpers,partials,data) {
    var stack1, helper;

  return "<td class=\"ci-description\" data-field=\"description\"><textarea>"
    + ((stack1 = ((helper = (helper = helpers.description || (depth0 != null ? depth0.description : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"description","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea></td>";
},"18":function(container,depth0,helpers,partials,data) {
    var stack1, helper;

  return "<td class=\"ci-category\" data-field=\"category\">"
    + ((stack1 = ((helper = (helper = helpers.category || (depth0 != null ? depth0.category : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"category","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</td>";
},"20":function(container,depth0,helpers,partials,data) {
    var helper;

  return "<td class=\"ci-cifield_1\" data-field=\"cifield_1\"><textarea>"
    + container.escapeExpression(((helper = (helper = helpers.cifield_1 || (depth0 != null ? depth0.cifield_1 : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"cifield_1","hash":{},"data":data}) : helper)))
    + "</textarea></td>";
},"22":function(container,depth0,helpers,partials,data) {
    var helper;

  return "<td class=\"ci-cifield_2\" data-field=\"cifield_2\"><textarea>"
    + container.escapeExpression(((helper = (helper = helpers.cifield_2 || (depth0 != null ? depth0.cifield_2 : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"cifield_2","hash":{},"data":data}) : helper)))
    + "</textarea></td>";
},"24":function(container,depth0,helpers,partials,data) {
    var helper;

  return "<td class=\"ci-cifield_3\" data-field=\"cifield_3\"><textarea>"
    + container.escapeExpression(((helper = (helper = helpers.cifield_3 || (depth0 != null ? depth0.cifield_3 : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"cifield_3","hash":{},"data":data}) : helper)))
    + "</textarea></td>";
},"26":function(container,depth0,helpers,partials,data) {
    var helper;

  return "<td class=\"ci-cifield_4\" data-field=\"cifield_4\"><textarea>"
    + container.escapeExpression(((helper = (helper = helpers.cifield_4 || (depth0 != null ? depth0.cifield_4 : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"cifield_4","hash":{},"data":data}) : helper)))
    + "</textarea></td>";
},"28":function(container,depth0,helpers,partials,data) {
    var helper;

  return "<td class=\"ci-cifield_5\" data-field=\"cifield_5\"><textarea>"
    + container.escapeExpression(((helper = (helper = helpers.cifield_5 || (depth0 != null ? depth0.cifield_5 : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"cifield_5","hash":{},"data":data}) : helper)))
    + "</textarea></td>";
},"30":function(container,depth0,helpers,partials,data) {
    var stack1;

  return ((stack1 = helpers.unless.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.is_linked : depth0),{"name":"unless","hash":{},"fn":container.program(31, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "");
},"31":function(container,depth0,helpers,partials,data) {
    return "<a href='#' class='gIcon ImageProcessAction'></a>";
},"33":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<a href='#' class='gIcon "
    + ((stack1 = helpers["if"].call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.is_cover : depth0),{"name":"if","hash":{},"fn":container.program(34, data, 0),"inverse":container.program(36, data, 0),"data":data})) != null ? stack1 : "")
    + "'></a>";
},"34":function(container,depth0,helpers,partials,data) {
    return "StarIcon ImageCover";
},"36":function(container,depth0,helpers,partials,data) {
    return "ImageCover";
},"38":function(container,depth0,helpers,partials,data) {
    return "<a href='#' class='gIcon ImageEdit'></a>";
},"40":function(container,depth0,helpers,partials,data) {
    return "<a href='#' class='gIcon ImageReplace'></a>";
},"42":function(container,depth0,helpers,partials,data) {
    return "class=\"gIcon ImageDel ImageLinked\"";
},"44":function(container,depth0,helpers,partials,data) {
    return "class=\"gIcon ImageDel\"";
},"46":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function";

  return "<li class=\"Image image-tile "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.is_cover : depth0),{"name":"if","hash":{},"fn":container.program(2, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\" data-filename=\""
    + ((stack1 = ((helper = (helper = helpers.filename || (depth0 != null ? depth0.filename : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"filename","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n    <a href='"
    + ((stack1 = ((helper = (helper = helpers.big_img_url || (depth0 != null ? depth0.big_img_url : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"big_img_url","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "' class='ImgUrl' rel='ChannelImagesGal' title='"
    + ((stack1 = ((helper = (helper = helpers.image_title || (depth0 != null ? depth0.image_title : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"image_title","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "'>\n        <img src=\""
    + ((stack1 = ((helper = (helper = helpers.small_img_url || (depth0 != null ? depth0.small_img_url : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"small_img_url","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\" alt=\""
    + ((stack1 = ((helper = (helper = helpers.image_title || (depth0 != null ? depth0.image_title : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"image_title","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n    </a>\n    <div class=\"filename\">\n        <div class=\"name\" data-field=\"title\"><input type=\"text\" value=\""
    + container.escapeExpression(((helper = (helper = helpers.image_title || (depth0 != null ? depth0.image_title : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"image_title","hash":{},"data":data}) : helper)))
    + "\" class=\"image_title\"></div>\n    </div>\n    <div class=\"actions\">\n        "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_cover : depth0),{"name":"if","hash":{},"fn":container.program(47, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n        <span "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.is_linked : depth0),{"name":"if","hash":{},"fn":container.program(49, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + " "
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.is_linked : depth0),{"name":"unless","hash":{},"fn":container.program(51, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "></span>\n        "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_image_edit : depth0),{"name":"if","hash":{},"fn":container.program(53, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n        "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.show_image_replace : depth0),{"name":"if","hash":{},"fn":container.program(55, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n    </div>\n\n    <textarea name=\""
    + ((stack1 = ((helper = (helper = helpers.field_name || (depth0 != null ? depth0.field_name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"field_name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "[images][][data]\" class=\"ImageData cihidden\">"
    + ((stack1 = ((helper = (helper = helpers.json_data || (depth0 != null ? depth0.json_data : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"json_data","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea>\n</li>\n";
},"47":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<span class=\"abtn btn-star "
    + ((stack1 = helpers["if"].call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.is_cover : depth0),{"name":"if","hash":{},"fn":container.program(34, data, 0),"inverse":container.program(36, data, 0),"data":data})) != null ? stack1 : "")
    + "\"></span>";
},"49":function(container,depth0,helpers,partials,data) {
    return "class=\"abtn ImageDel ImageLinked\"";
},"51":function(container,depth0,helpers,partials,data) {
    return "class=\"abtn btn-delete ImageDel\"";
},"53":function(container,depth0,helpers,partials,data) {
    return "<span class=\"abtn btn-edit ImageEdit\"></span>";
},"55":function(container,depth0,helpers,partials,data) {
    return "<span class=\"abtn btn-replace ImageReplace\"></span>";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, alias1=depth0 != null ? depth0 : {};

  return ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.table_view : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.tile_view : depth0),{"name":"if","hash":{},"fn":container.program(46, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "");
},"useData":true});