<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="Styles/TableStyle.css">
        <link rel="stylesheet" href="Styles/DashboardCSS.css">
        <link rel="stylesheet" href="Styles/newcss.css">
        <link rel="shortcut icon" href="http://omnicongroup.com/images/header/logo_header2.jpg">
    </head>
    <style>
	/* override the vertical-align top in the blue theme */
	.notes.tablesorter-blue tbody td { vertical-align: middle; }
	</style>
	<script>
	$(function(){
		$('.options').tablesorter({
			widthFixed: true,
			widgets: ['stickyHeaders']
		});
	});
	</script>
        <br>
    <body>
        <div class= "main">
        <?php
        //Adds Omnicon Dashboard Header
        include 'header.php';
        ?> 
            <div id="viewTitle"><a href="javascript:history.go(-1)">Go Back</a></div>
            <br>
            <div class="centered">
                
            <table class="tablesorter-blue notes" style="width:80%" >
						<thead>
							<tr>
								<th style="width:10%">Type <small class="bright">(1)(2)</small></th>
								<th style="width:30%">Description</th>
								<th style="width:40%">Examples</th>
							</tr>
						</thead>
						<tbody>
							<tr><td class="center">text</td><td>Any text entered in the filter will <strong>match</strong> text found within the column</td><td><code>abc</code> (finds &quot;abc&quot;, &quot;abcd&quot;, &quot;abcde&quot;, etc);<button data-filter-column="1">Aaron</button> (finds &quot;Aaron&quot; and &quot;Philip Aaron Wong&quot;)</td></tr>
							<tr><td class="center"><code>/\d/</code></td><td>Add any regex to the query to use in the query ("mig" flags can be included <code>/\w/mig</code>)</td><td><code>/b[aeiou]g/i</code> (finds &quot;bag&quot;, &quot;beg&quot;, &quot;BIG&quot;, &quot;Bug&quot;, etc);<button data-filter-column="1">/r$/</button> (matches text that ends with an &quot;r&quot;)</td></tr>
							<tr><td class="center"><code>&lt; &lt;= &gt;= &gt;</code></td><td>Find alphabetical or numerical values less than or greater than or equal to the filtered query <small class="bright">(2)</small>.</td><td><button data-filter-column="5">&gt;= 10</button> (find values greater than or equal to 10)</td></tr>
							<tr><td class="center"><code>!</code> or <code>!=</code></td><td>Not operator, or not exactly match. Filter the column with content that <strong>do not</strong> match the query. Include an equal (<code>=</code>), single (<code>'</code>) or double quote (<code>&quot;</code>) to exactly <em>not</em> match a filter (<span class="version">v2.17.1</span>).</td><td><code>!fe</code> (hide rows with &quot;female&quot; in that column, but shows rows with &quot;male&quot;);<button data-filter-column="1">!a</button> (find text that doesn't contain an &quot;a&quot;);<button data-filter-column="1">!"Bruce"</button> (find content that does not exactly match "Bruce")</td></tr>
							<tr><td class="center"><code>&quot;</code> or <code>=</code></td><td>To exactly match the search query, add a quote, apostrophe or equal sign to the beginning and/or end of the query</td><td><code>abc&quot;</code> or <code>abc=</code> (exactly match &quot;abc&quot;);<button data-filter-column="1">John&quot;</button> or <button data-filter-column="1">John=</button> (exactly match &quot;John&quot;)</td></tr>
							<tr><td class="center"><code>&nbsp;&&&nbsp;</code> or <code>&nbsp;AND&nbsp;</code></td><td>Logical &quot;and&quot;. Filter the column for content that matches text from either side of the operator.</td><td><code>box && bat</code> (matches a column cell that contains both &quot;box&quot; and &quot;bat&quot;);<button data-filter-column="1">Br && c</button> (Find text that contains both &quot;br&quot; and &quot;c&quot;)</td></tr>
							<tr><td class="center"><code>&nbsp;-&nbsp;</code> or <code>&nbsp;to&nbsp;</code></td><td>Find a range of values. Make sure there is a space before and after the dash (or the word &quot;to&quot;) <small class="bright">(4)</small>.</td><td><button data-filter-column="3">10 - 30</button> or <button data-filter-column="4">10 to 30</button> (match values between 10 and 30)</td></tr>
							<tr><td class="center"><code>?</code></td><td>Wildcard for a single, non-space character.</td><td><code>J?n</code> (finds &quot;Jan&quot; and &quot;Jun&quot;, but not &quot;Joan&quot;);<button data-filter-column="2">a?s</button> (finds &quot;Evans&quot;, but not &quot;McMasters&quot;)</td></tr>
							<tr><td class="center"><code>*</code></td><td>Wildcard for zero or more non-space characters.</td><td><code>B*k</code> (matches &quot;Black&quot; and &quot;Book&quot;);<button data-filter-column="2">a*s</button> (matches &quot;Evans&quot; and &quot;McMasters&quot;)</td></tr>
							<tr><td class="center"><code>|</code> or <code>&nbsp;OR&nbsp;</code></td><td>Logical &quot;or&quot; (Vertical bar). Filter the column for content that matches text from either side of the bar <small class="bright">(3)</small>.</td><td><code>box|bat</code> (matches a column cell with either &quot;box&quot; or &quot;bat&quot;);<button data-filter-column="1">Alex|Peter</button> (Find text that contains either &quot;Alex&quot; or &quot;Peter&quot;)</td></tr>
							<tr><td class="center"><code>~</code></td><td>Perform a fuzzy search (matches sequential characters) by adding a tilde to the beginning of the query (<span class="version">v2.13.3</span>)</td><td><button data-filter-column="1">~bee</button> (matches &quot;Bruce Lee&quot; and &quot;Brenda Dexter&quot;), or <button data-filter-column="1">~piano</button> (matches &quot;Philip Aaron Wong&quot;)</td></tr>
                                                <br></tbody>
					</table>
            </div>
        </div>
    </body>
</html>
