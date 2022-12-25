O:10:"feedParser":7:{s:9:"feed_type";s:8:"atom 1.0";s:5:"title";s:20:"Dotclear News - News";s:4:"link";s:26:"https://dotclear.org/blog/";s:11:"description";s:25:"Blog management made easy";s:7:"pubdate";s:25:"2022-12-24T08:44:51+01:00";s:9:"generator";s:8:"Dotclear";s:5:"items";a:20:{i:0;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2022/12/24/Dotclear-2.24";s:5:"title";s:13:"Dotclear 2.24";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:1650:"    <p>The new version for the holidays. It is <strong>strongly</strong> recommended that you do <a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24">the update in safe mode</a>, which will then allow you to update the plugins that need to be updated.</p>

<p>If you have trouble logging in after the update, delete the associated cookies before refreshing the login page.</p>

<p>The 2.24 CHANGELOG:</p>

<ul>
<li>🐘 PHP 7.4+ is required, PHP 8.0/8.1 compliance</li>
<li>🗑 Remove XML/RPC system (keep only minimum for Pingbacks)</li>
<li>New blog parameter to close comments/trackbacks after a period of inactivity on the blog</li>
<li>Core: Large code review has been done, may break old code (3rd party plugins and themes)</li>
<li>Admin UI: New default icons for media items</li>
<li>Admin UI: Message look reviewed</li>
<li>Admin UX: Preserve current dir and current view of media manager</li>
<li>Admin UX: Password strength use an entropy indicator</li>
<li>Admin UX: Improve navigation in about:config and user:preferences list</li>
<li>Admin UX: Allow activation and de-activation of plugins in safe mode</li>
<li>Admin UX: Allow update of disabled/activated plugins in safe mode/normal mode</li>
<li>Admin UX: Add folding capability to widgets group</li>
<li>Theme: Cope with theme defined widget container format</li>
<li>Theme: Smilies are available for every theme (Blowup theme not more mandatory)</li>
<li>Lib: Update CKEditor to 4.20.1</li>
<li>Lib: Update Codemirror to 5.65.10</li>
<li>🐛 → Various bugs, a11y concerns and typos fixed</li>
<li>🌼 → Some locales and cosmetic adjustments</li>
</ul>
";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-12-24T10:00:00+01:00";s:2:"TS";i:1671872400;}i:1;O:8:"stdClass":8:{s:4:"link";s:63:"https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24";s:5:"title";s:21:"How to update to 2.24";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:4912:"    <p><img src="https://dotclear.org/public/feuille-dc.svg" alt="" style="width: 10em; margin: 0 auto; display: block;" /></p>

<p>We won't lie to ourselves, the next update could be ... sporty<sup id="fnref:4"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:4" rel="footnote">1</a></sup> :-)</p>

<p>I just tested several times, from an installation that runs in production (my <a href="https://open-time.net/">blog</a>) with Dotclear 2.23.1 and I noticed the following things:</p>

<ol>
<li><p>If you ever use the <a href="https://plugins.dotaddict.org/dc2/details/staticCache">static cache</a><sup id="fnref:2"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:2" rel="footnote">2</a></sup> plugin, <strong>disable</strong> it temporarily (just comment out the <code>DC_SC_CACHE_ENABLE</code> activation constant in the <var>inc/config.php</var> file, or set it to <code>false</code>).</p></li>
<li><p>Upgrading to Dotclear 2.24 in <strong>rescue mode</strong> is the best way to do it, once you know<sup id="fnref:3"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:3" rel="footnote">3</a></sup> that the plugins you use are <strong>available</strong> for 2.24<sup id="fnref:1"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:1" rel="footnote">4</a></sup>.</p></li>
<li><p>If you ever need the <a href="https://plugins.dotaddict.org/dc2/details/fakemeup">FakeMeUp</a> plugin, then log back in to normal mode, install it, run it, then log back in to rescue mode.</p></li>
<li><p>Make way for <strong>updates</strong>:</p>

<ol>
<li>Upgrade <strong>Dotclear to 2.24</strong> (still in <strong>backup mode</strong>),</li>
<li>Reconnect in <strong>backup mode</strong> because the previous step will bring you back to the authentication page<sup id="fnref:8"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:8" rel="footnote">5</a></sup>,</li>
<li>Do the <strong>update plugins</strong><sup id="fnref:7"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:7" rel="footnote">6</a></sup>,</li>
<li><strong>Reactivate</strong> the static cache if necessary,</li>
<li>Reconnect in <strong>normal mode</strong>.</li>
</ol></li>
</ol>

<p>This should be all good!</p>

<p>Take the opportunity to <strong>clear the template cache</strong> and <strong>the static cache</strong> (Maintenance plugin).</p>

<p>A little extra: it may be useful to install the <a href="https://plugins.dotaddict.org/dc2/details/growUp">growUp</a> plugin to clean up a bit<sup id="fnref:5"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:5" rel="footnote">7</a></sup> once the update is done<sup id="fnref:6"><a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fn:6" rel="footnote">8</a></sup>.</p>

<p>Anyway, we'll be around if there is a problem; on <a href="https://forum.dotclear.org/">the forum</a> in particular.</p>

<div class="footnotes">
<hr />
<ol>

<li id="fn:4">
<p>To be honest my first attempt ran into a problem with the static cache (see item 1 in the list), otherwise I was able to do the update in normal mode without any trouble.&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:4" rev="footnote">&#8617;</a></p>
</li>

<li id="fn:2">
<p>Especially if you use it, like me, aggressively.&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:2" rev="footnote">&#8617;</a></p>
</li>

<li id="fn:3">
<p>Install the <a href="https://plugins.dotaddict.org/dc2/details/checkStoreVersion">Check store version</a> plugin, it will tell you all that.&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:3" rev="footnote">&#8617;</a></p>
</li>

<li id="fn:1">
<p>If not, wait until they are, it's better!&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:1" rev="footnote">&#8617;</a></p>
</li>

<li id="fn:8">
<p>The authentication page may not be displayed, in which case delete the cookies associated with the site and refresh the page.&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:8" rev="footnote">&#8617;</a></p>
</li>

<li id="fn:7">
<p>Force the update check to make sure you don't miss anything.&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:7" rev="footnote">&#8617;</a></p>
</li>

<li id="fn:5">
<p>This is normally done during the update, but sometimes things can happen ;-)&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:5" rev="footnote">&#8617;</a></p>
</li>

<li id="fn:6">
<p>It can be disabled or uninstalled afterwards.&#160;<a href="https://dotclear.org/blog/post/2022/12/13/How-to-update-to-2.24#fnref:6" rev="footnote">&#8617;</a></p>
</li>

</ol>
</div>
";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-12-13T15:26:00+01:00";s:2:"TS";i:1670941560;}i:2;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2022/08/13/Dotclear-2.23.1";s:5:"title";s:15:"Dotclear 2.23.1";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:101:"    <p>And in the meantime, a maintenance version to correct a bug with the addition of comments.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-08-13T10:02:00+02:00";s:2:"TS";i:1660377720;}i:3;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2022/08/13/Dotclear-2.23";s:5:"title";s:13:"Dotclear 2.23";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:2390:"    <p>The new version for this quarter with some improvements in the program mainly in the core code, but not only.</p>


<p>Note the disappearance of the management of icon sets, little used and potentially complicated with the use of icons in SVG format in two versions (light and dark theme).</p>


<p>The CHANGELOG of 2.23&nbsp;:</p>

<ul>
<li>🐘 PHP 7.4+ is required, PHP 8.0/8.1 compliance</li>
<li>🗑 Remove Iconset management</li>
<li>Admin UI: Harmonize font size on different support (laptop, tablet, mobile)</li>
<li>Admin UX: Group more logically buttons on CKEditor toolbar</li>
<li>Core: New constant DC_DEFAULT_THEME, set to 'berlin'</li>
<li>Core: Use predefined constants for post statuses (dcBlog::POST_*)</li>
<li>Core: Use predefined constants for comment statuses (dcBlog::COMMENT_*)</li>
<li>Core: Deprecated global $core (or $GLOBALS<a href="https://dotclear.org/blog/post/2022/08/13/&#039;core&#039;" title="&#039;core&#039;">'core'</a>), use dcCore::app() instead</li>
<li>Core: Deprecated global $_ctx, use dcCore::app()-&gt;ctx instead</li>
<li>Core: Deprecated global $_lang, use dcCore::app()-&gt;lang instead</li>
<li>Core: Deprecated global $mod_files, use dcCore::app()-&gt;cache<a href="https://dotclear.org/blog/post/2022/08/13/&#039;mod_files&#039;" title="&#039;mod_files&#039;">'mod_files'</a> instead</li>
<li>Core: Deprecated global $mod_ts, use dcCore::app()-&gt;cache<a href="https://dotclear.org/blog/post/2022/08/13/&#039;mod_ts&#039;" title="&#039;mod_ts&#039;">'mod_ts'</a> instead</li>
<li>Core: Deprecated global $_menu, use dcCore::app()-&gt;menu instead</li>
<li>Core: Deprecated global $__resources, use dcCore::app()-&gt;resources instead</li>
<li>Core: REST server now accepts JSON format (experimental)</li>
<li>Fix: Use relative URL for attachments as far as possible</li>
<li>Fix: Remove select hiding mechanism when help is displayed</li>
<li>Fix: Loading of modules (plugins/themes) in safe mode</li>
<li>Fix: Message position on Quick entry submit (dashboard)</li>
<li>Fix: Select appearance on Safari (webkit engine)</li>
<li>Lib: Update CKEditor to 4.19.1</li>
<li>Lib: Update Codemirror to 5.65.7</li>
<li>🐛 → Various bugs, a11y concerns and typos fixed</li>
<li>🌼 → Some locales and cosmetic adjustments</li>
<li>📣 Warning: Internet Explorer is not more officially supported (may still work weirdly)</li>
</ul>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-08-13T09:02:00+02:00";s:2:"TS";i:1660374120;}i:4;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2022/05/13/Dotclear-2.22";s:5:"title";s:13:"Dotclear 2.22";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:825:"    <p>The new version for this quarter — we're keeping up, that's a good thing — with some improvements to the program:</p>

<ul>
<li>🐘 PHP 7.4+ is required, PHP 8.0/8.1 compliance</li>
<li>Remove anti-FLoC system</li>
<li>Add a live preview button to standard Dotclear editor (wiki syntax)</li>
<li>Use native Javascript in scripts shiped with Berlin and Ductile theme (no more need jQuery)</li>
<li>Improve retrieval of origin metadata on Webmention or Pingback</li>
<li>Add a "Reset to now" button near the publish datetime input field (post/page)</li>
<li>Reduce number of CSS mediaqueries' breakpoints to 3 (mobile, tablet, laptop) for backend</li>
<li>Add a sticky position to "quick access to section" menu for about:Config and user:Prefs</li>
<li>Toolbar icons reviewed for standard Dotclear editor</li>
</ul>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-05-13T08:19:00+02:00";s:2:"TS";i:1652422740;}i:5;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2022/03/07/Dotclear-2.21.3";s:5:"title";s:15:"Dotclear 2.21.3";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:132:"    <p>A new version that fixes two bugs concerning the management of users other than administrators (or super-administrators).</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-03-07T10:18:00+01:00";s:2:"TS";i:1646644680;}i:6;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2022/02/26/Dotclear-2.21.2";s:5:"title";s:15:"Dotclear 2.21.2";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:315:"    <p>A new maintenance release that solves, among other things, the date and cache problems encountered by some users.</p>


<p>Changes:</p>

<ul>
<li>Revert some modifications done for PHP 8.1 compliance (strftime)</li>
<li>Cleanup remaining currywurst folders (currywurst template removed since 2.20)</li>
</ul>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-02-26T09:31:00+01:00";s:2:"TS";i:1645864260;}i:7;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2022/02/19/Dotclear-2.21.1";s:5:"title";s:15:"Dotclear 2.21.1";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:226:"    <p>A maintenance release to fix these bugs:</p>

<ul>
<li>Fix: Cope with author TZ for posts and pages edition</li>
<li>Fix: Avoid browser caching on page/post preview</li>
<li>Fix: List of entries using a media</li>
</ul>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-02-19T08:26:00+01:00";s:2:"TS";i:1645255560;}i:8;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2022/02/13/Dotclear-2.21";s:5:"title";s:13:"Dotclear 2.21";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:699:"    <p>A new version of Dotclear which I let you discover the (light) resumption of the look of the administration. In particular, many PNG images have been replaced by a vector version (SVG format), which allows to keep a good quality at any zoom level.</p>


<p>Note that it requires PHP 7.4 or PHP 8.0. We have tried to fix all the problems that may occur with PHP 8.1, but our testing may not have been exhaustive and we welcome any feedback on how this latest version of PHP works.</p>


<p>For details of the changes, see the <a href="https://git.dotclear.org/dev/dotclear/src/branch/2.21/CHANGELOG">CHANGELOG</a> or even the <a href="https://git.dotclear.org/dev/dotclear">repository</a>.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2022-02-13T08:02:00+01:00";s:2:"TS";i:1644735720;}i:9;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2021/11/19/Dotclear-2.20.1";s:5:"title";s:15:"Dotclear 2.20.1";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:113:"    <p>A small update that fixes three not very serious but potentially annoying bugs in the use of Dotclear.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2021-11-19T13:47:00+01:00";s:2:"TS";i:1637326020;}i:10;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2021/11/13/Dotclear-2.20";s:5:"title";s:13:"Dotclear 2.20";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:6798:"    <p><img src="https://dotclear.org/public/images/.still-gardening_m.jpg" alt="" /></p>


<p>Still gardening and happy tooyou <a href="http://www.kozlika.org/kozeries/">Kozlika</a>!</p>


<hr />


<p>As <a href="https://dotclear.org/blog/post/2021/08/13/Dotclear-2.19">announced</a> at the time of the 2.19 release, we are publishing new versions more often - or at least trying to.</p>


<p>In this new version 2.20, the highlights are as follows:</p>

<ul>
<li>A new <strong>alternative repository</strong> system has been set up for third-party <strong>plugins</strong> and <strong>themes</strong>, which can be useful if the DotAddict server is running out of steam, as it did recently (thanks to <a href="https://www.noecendrier.fr/" hreflang="fr">Noé</a> for getting it up and running again), or if the author does not wish to deposit his work elsewhere than on his own public repository. We detail the procedure to follow below.</li>
<li>A new I<strong>Pv6</strong>-specific spam filter (which is starting to be deployed quite a bit) is included in parallel with the IPv4-specific filter.</li>
<li>Users can now enter <strong>several additional</strong> email addresses and websites in their profile. Indeed, some themes allow the author of a post to be differentiated from other commenters on the basis of those email and web site addresses, which may change over time. This system therefore makes it possible to indicate new addresses without having to modify the metadata of old comments.</li>
<li>Dotclear's <strong>wiki</strong> syntax has been extended to allow the easy insertion of HTML block <code>details</code>. A vertical bar at the beginning of the line, followed by the text of the summary is necessary to start this block, followed by the free content of the block, followed by a line with a vertical bar as the first character only ending the whole, i.e.&nbsp;:</li>
</ul>
<pre>
|summary of the detail block (hidden by default)
    …
content of my block
    …
|
</pre>


<p>Please note: this version is the <strong>last</strong> to support <strong>PHP 7.3</strong>; the next <strong>2.21</strong> will require at least PHP <strong>7.4</strong> (or PHP 8). A message will be displayed on your dashboard if your PHP version is affected.</p>


<hr />


<h3>Alternative repositories:</h3>


<p>To implement an alternative repository for a module, plugin or theme, you need two things:</p>

<ol>
<li>A <strong>repository</strong> entry in the properties provided in the module's <code>_define.php</code> file, such as: <code>'repository' =&gt; 'https://raw.githubusercontent.com/franck-paul/sysInfo/main/dcstore.xml'</code></li>
<li>A <code>dcstore.xml</code> file structured as follows, and stored in accordance with the URL provided above:</li>
</ol>
<pre>
&lt;modules xmlns:da=&quot;http://dotaddict.org/da/&quot;&gt;
  &lt;module id=&quot;[MODULE_ID]&quot;&gt;
    &lt;name&gt;[MODULE NAME]&lt;/name&gt;
    &lt;version&gt;[MODULE.VERSION]&lt;/version&gt;
    &lt;author&gt;[MODULE AUTHOR]&lt;/author&gt;
    &lt;desc&gt;[MODULE DESCRIPTION]&lt;/desc&gt;
    &lt;file&gt;[MODULE_ARCHIVE.ZIP]&lt;/file&gt;
    &lt;da:dcmin&gt;[MODULE_DOTCLEAR_VERSION_MIN]&lt;/da:dcmin&gt;
    &lt;da:details&gt;[MODULE_DETAIL_URL]&lt;/da:details&gt;
    &lt;da:support&gt;[MODULE_SUPPORT_URL]&lt;/da:support&gt;
  &lt;/module&gt;
&lt;/modules&gt;
</pre>


<p>Example for the <a href="https://plugins.dotaddict.org/dc2/details/sysInfo">sysInfo</a> plugin:</p>

<pre>
&lt;modules xmlns:da=&quot;http://dotaddict.org/da/&quot;&gt;
  &lt;module id=&quot;sysInfo&quot;&gt;
    &lt;name&gt;System Information&lt;/name&gt;
    &lt;version&gt;1.16.3&lt;/version&gt;
    &lt;author&gt;System Information&lt;/author&gt;
    &lt;desc&gt;System Information&lt;/desc&gt;
    &lt;file&gt;https://github.com/franck-paul/sysInfo/releases/download/1.16.3/plugin-sysInfo-1.16.3.zip&lt;/file&gt;
    &lt;da:dcmin&gt;2.19&lt;/da:dcmin&gt;
    &lt;da:details&gt;https://open-time.net/docs/plugins/sysInfo&lt;/da:details&gt;
    &lt;da:support&gt;https://github.com/franck-paul/sysInfo&lt;/da:support&gt;
  &lt;/module&gt;
&lt;/modules&gt;
</pre>


<p>Note that the <code>dcstore.xml</code> file does not need to be included in the module installation archive.</p>


<p>As soon as a module, indicating in its <code>_define.php</code> file an alternative repository, will be installed with Dotclear version 2.20, then the latter will also consult this repository to check for the presence of a new version.</p>


<hr />


<h3>One more thing!</h3>


<p>It is possible to save the <strong>default settings</strong> for inserting a media file (image, sound, ...) which is then used when editing posts and pages. See Blog settings, section "Media and images". It is also possible to save the current insertion parameters when inserting media into a post.</p>


<p>This is convenient but can be counterproductive in some cases.</p>


<p>Dotclear version 2.20 now takes into account the presence of a <code>.mediadef</code> file (or <code>.mediadef.json</code>) structured as follows, so that the settings specified in it become automatically pre-selected instead of those saved by default for the blog:</p>

<pre>
{
&quot;size&quot;: &quot;o&quot;,
&quot;legend&quot;: &quot;none&quot;,
&quot;alignment&quot;: &quot;center&quot;,
&quot;link&quot;: false
}
</pre>


<p>Voilà les valeurs possibles pour les différents réglages&nbsp;:</p>

<ul>
<li><code>size</code>&nbsp;: <samp>"sq"</samp> for <strong>thumbnail</strong>, <samp>"s"</samp> for <strong>small</strong>, <samp>"m"</samp> for <strong>medium</strong>, <samp>"o"</samp> for <strong>original</strong></li>
<li><code>legend</code>&nbsp;: <samp>"none"</samp> for <strong>none</strong>, <samp>"title"</samp> for <strong>title</strong> only, <samp>"legend"</samp> for <strong>title and legend</strong></li>
<li><code>alignment</code>&nbsp;: <samp>"none"</samp> for <strong>none</strong>, <samp>"left"</samp> to <strong>left</strong> align, <samp>"right"</samp> to <strong>right</strong> align, <samp>"center"</samp> to <strong>center</strong></li>
<li><code>link</code>&nbsp;: <samp>true</samp> <strong>with</strong> the link, <samp>false</samp> <strong>without</strong> the original image link</li>
</ul>

<p>You are not obliged to specify all the settings and if one or more of them are missing, the one or more saved for the blog will be used.</p>


<p>Moreover, this preset file is <strong>only</strong> valid for the folder in which it is saved and therefore only for the media it contains.</p>


<hr />


<h3>Conclusion</h3>


<p>For the rest, the curious can consult the details of the modifications in the <a href="https://git.dotclear.org/dev/dotclear/src/branch/2.20/CHANGELOG">CHANGELOG</a> file of this version.</p>


<p><em>Et voilà !</em></p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2021-11-13T06:35:00+01:00";s:2:"TS";i:1636781700;}i:11;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2021/08/13/Dotclear-2.19";s:5:"title";s:13:"Dotclear 2.19";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:1289:"    <p>A new version to celebrate the 18 years of Dotclear.</p>


<p>On the program, a more robust code (PHP and Javascript), some improvements for themes developers, a minimal version of <strong>PHP 7.3</strong> required, the compatibility with PHP 8 being ensured, the few used libraries have been updated (jQuery, CKEditor, codemirror, ...).</p>


<p>Note that the <strong>MySQL</strong> driver support has been removed and is now replaced by the <strong>MySQLi</strong> driver. You don't have to change anything if you were using the old one, the replacement is automatic.</p>


<p>Furthermore, the "remember me" function present on the blog comment forms, previously managed via the creation of a cookie, is now replaced by a local storage in the browser via the <strong>localStorage</strong> API.</p>


<p>Note also that <strong>Google's FLoC</strong> tracking system is automatically disabled (which can be overridden via the blog settings).</p>


<p>The curious can study the <code><a href="https://git.dotclear.org/dev/dotclear/commit/df16306eb1ff386012f1bdc69d2ae933fe354613">CHANGELOG</a></code> file for details.</p>


<p>We will also try to publish new versions more often with probably less stuff each time as the application has already reached its maturity/majority :-)</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2021-08-13T16:36:00+02:00";s:2:"TS";i:1628865360;}i:12;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2021/02/13/Dotclear-2.18.1";s:5:"title";s:15:"Dotclear 2.18.1";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:109:"    <p>A maintenance version that corrects a few bugs, especially when putting programmed entries online.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2021-02-13T11:28:00+01:00";s:2:"TS";i:1613212080;}i:13;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2020/11/13/Dotclear-2.18";s:5:"title";s:13:"Dotclear 2.18";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:513:"    <p>A new version that brings some changes and updates.</p>


<p>The most notable are&nbsp;:</p>

<ol>
<li>The IP addresses - especially from comments - are now displayed in the administration only if you are administrator or super-administrator.</li>
<li>The HTML syntax and the CKEditor editor are now proposed by default for new users and new blogs.</li>
<li>The CKEditor editor now integrates footnotes management.</li>
</ol>

<p>Note that the next major release, 2.19, will require PHP 7.0 or greater!</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2020-11-13T13:04:00+01:00";s:2:"TS";i:1605269040;}i:14;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2020/08/17/Dotclear-2.17.2";s:5:"title";s:15:"Dotclear 2.17.2";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:75:"    <p>A maintenance version that fixes two minor problems with Safari.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2020-08-17T10:21:00+02:00";s:2:"TS";i:1597652460;}i:15;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2020/08/15/Dotclear-2.17.1";s:5:"title";s:15:"Dotclear 2.17.1";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:120:"    <p>A maintenance version to fix a problem caused by Chrome with the optional password fields of posts and pages.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2020-08-15T09:53:00+02:00";s:2:"TS";i:1597477980;}i:16;O:8:"stdClass":8:{s:4:"link";s:55:"https://dotclear.org/blog/post/2020/08/13/Dotclear-2.17";s:5:"title";s:13:"Dotclear 2.17";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:2081:"    <p>Here's the “Jurassic blog edition”, aka Dotclear 2.17 to celebrate 17 years of Dotclear today \o/</p>


<hr />


<p>The CHANGELOG:</p>

<pre>
* 🐘 PHP 5.6+ is required, PHP 7.4 compliance
* 🛡 Security: Password is now needed to export blog settings and contents (full/simple)
* Themes can now be cloned
* New helper button (show/hide) for password fields
* Enhancement of filter/sort usage for lists (posts, comments, …)
* 3rd automatic theme for backend theme (which follow OS setting)
* Authentication (backend) and password form (public for password protected entry) have been redesigned
* Add a Cancel button wherever relevant in backend
* PHP files can now be edited in Theme editor
* Plugins may now use SVG icon rather than JPG/PNG
* Black/White list names become Block/Allow list (antispam)
* Wiki: subscript syntax changed from _subscript_ to ,,subscript,,
* Wiki: add ;;span-content;; syntax
* Wiki: add §§attributes[|list attributes]§§ for blocks (at end of the 1st line of block)
* Wiki: add §attributes§ for inline elements (just before closing marker, warning: cannot be nested)
* Tpl: Add {{tpl:BlogNbEntriesFirstPage}} and {{tpl:BlogNbEntriesPerPage}}
* Tpl: Add optional even attribute to &lt;tpl:EntryIfOdd&gt;, &lt;tpl:CommentIfOdd&gt; and &lt;tpl:PingIfOdd&gt;
* Tpl: Add author=&quot;…&quot; as attribute of &lt;tpl:EntryIf&gt;
* Sys: Add several behaviors, coreBeforeImageMetaCreate, themeBeforeClone and themeAfterClone
* a11y: Reduce motion if required in provided themes and backend
* Lib: Update jQuery to 3.5.1 (backend and public)
* Lib: Update Codemirror to 5.55.0
* Lib: CKEditor new color palette (configurable)
* Fix: Notification system refactored (now based on db rather than PHP Session)
* Fix: Missing confirmation before closing modified forms / unecessary confirmation asked before closing not modified forms
* i18n: Switch from Transifex to Crowdin for localisation purpose (https://dotclear.crowdin.com/)
* 🐛 → Various bugs, a11y concerns and typos fixed
* 🌼 → Some locales and cosmetic adjustments
</pre>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2020-08-13T10:18:00+02:00";s:2:"TS";i:1597306680;}i:17;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2020/06/02/Dotclear-2.16.9";s:5:"title";s:15:"Dotclear 2.16.9";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:59:"    <p>A new little version that fixes some minor bugs.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2020-06-02T16:04:00+02:00";s:2:"TS";i:1591106640;}i:18;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2020/05/27/Dotclear-2.16.8";s:5:"title";s:15:"Dotclear 2.16.8";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:102:"    <p>This version fixes the use of the Clearbricks library, not updated in the previous version.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2020-05-27T15:37:00+02:00";s:2:"TS";i:1590586620;}i:19;O:8:"stdClass":8:{s:4:"link";s:57:"https://dotclear.org/blog/post/2020/05/27/Dotclear-2.16.7";s:5:"title";s:15:"Dotclear 2.16.7";s:7:"creator";s:6:"Franck";s:11:"description";s:0:"";s:7:"content";s:103:"    <p>As the previous one, a new little version that fixes some minor but sometimes annoying bugs.</p>";s:7:"subject";a:1:{i:0;s:4:"News";}s:7:"pubdate";s:25:"2020-05-27T10:25:00+02:00";s:2:"TS";i:1590567900;}}}