<%IT:SimpleArrayOutput item_begin="<span class=&quot;error&quot;>" item_end="</span><br />" array="it__int_user_module_messages" /%>
<table cellspacing="0" cellpadding="0" class="log">
<tr>
    <td class="log_top_l"></td>
    <td class="log_top_c"></td>
    <td class="log_top_r"></td>
</tr>
<tr>
    <td class="log_m_l"></td>
    <td class="log_m_c"><br />
        <%IT:Localizer string="login_form_title" /%>
        <%IT:form name="login_form" begin="true" /%>
        <table cellspacing="6" cellpadding="0" class="maxw">
        <tr>
          <td colspan="2" align="left"></td>
        </tr>
        <tr>
            <td align="left" nowrap="nowrap"><strong><%IT:Localizer string="login_name_title" /%>:</strong></td>
            <td align="right"><%IT:input type="text" name="login_form_name" class="inp" title="login" /%>hgfd</td>
        </tr>
        <tr>
            <td align="left" nowrap="nowrap"><strong><%IT:Localizer string="login_password_title" /%>:</strong></td>
            <td align="right"><%IT:input type="password" name="login_form_password" title="password" class="inp" /%></td>
        </tr>
        <tr>
            <td></td>
            <td align="right" class="log_sep" nowrap="nowrap">
                <input name="ctl00$ctl01$chkRemember" type="checkbox" id="ctl00_ctl01_chkRemember" tabindex="1" />remember me&nbsp;&nbsp;&nbsp;&nbsp;
                <label>
                    <input type="submit" class="butt hand" onmouseover="this.className='butt_act hand';" onmouseout="this.className='butt hand';" value="<%IT:Localizer string="login_login_button" /%>" />
                </label></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        </table>
        <%IT:form end="true" /%>
    </td>
    <td class="log_m_r"></td>
</tr>
<tr>
    <td class="log_bot_l"></td>
    <td class="log_bot_c"></td>
    <td class="log_bot_r"></td>
</tr>
</table>