/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* these are functions used by the table sorter on the view forms.  They are the same on all view forms, so are moved into this js file */


$(function() {
		$.extend( $.tablesorter.defaults, {
			theme: 'blue'
                        
		});
	});
   
        
$(function() {
        
        // call the tablesorter plugin
	$("#table").tablesorter({
                
                theme: 'blue',	
                // third click on the header will reset column to default - unsorted
                sortReset   : true,
                // hidden filter input/selects will resize the columns, so try to minimize the change
		widthFixed : true,

		// initialize zebra striping and filter widgets
		widgets: ["zebra", "filter"],

		ignoreCase: false,
                

		widgetOptions : {

			// filter_anyMatch options was removed in v2.15; it has been replaced by the filter_external option

			// If there are child rows in the table (rows with class name from "cssChildRow" option)
			// and this option is true and a match is found anywhere in the child row, then it will make that row
			// visible; default is false
			filter_childRows : false,

			// if true, a filter will be added to the top of each table column;
			// disabled by using -> headers: { 1: { filter: false } } OR add class="filter-false"
			// if you set this to false, make sure you perform a search using the second method below
			filter_columnFilters : true,

			// extra css class name(s) applied to the table row containing the filters & the inputs within that row
			// this option can either be a string (class applied to all filters) or an array (class applied to indexed filter)
			filter_cssFilter : '', // or []

			// jQuery selector (or object) pointing to an input to be used to match the contents of any column
			// please refer to the filter-any-match demo for limitations - new in v2.15
			filter_external : '',

			// class added to filtered rows (rows that are not showing); needed by pager plugin
			filter_filteredRow   : 'filtered',

			// add custom filter elements to the filter row
			// see the filter formatter demos for more specifics
			filter_formatter : null,

			// add custom filter functions using this option
			// see the filter widget custom demo for more specifics on how to use this option
			filter_functions : null,

			// if true, filters are collapsed initially, but can be revealed by hovering over the grey bar immediately
			// below the header row. Additionally, tabbing through the document will open the filter row when an input gets focus
			filter_hideFilters : true,

			// Set this option to false to make the searches case sensitive
			filter_ignoreCase : true,

			// if true, search column content while the user types (with a delay)
			filter_liveSearch : true,

			// a header with a select dropdown & this class name will only show available (visible) options within that drop down.
			filter_onlyAvail : 'filter-onlyAvail',

			// jQuery selector string of an element used to reset the filters
			filter_reset : 'button.reset',

			// Use the $.tablesorter.storage utility to save the most recent filters (default setting is false)
			filter_saveFilters : true,

			// Delay in milliseconds before the filter widget starts searching; This option prevents searching for
			// every character while typing and should make searching large tables faster.
			filter_searchDelay : 300,

			// if true, server-side filtering should be performed because client-side filtering will be disabled, but
			// the ui and events will still be used.
			filter_serversideFiltering: false,

			// Set this option to true to use the filter to find text from the start of the column
			// So typing in "a" will find "albert" but not "frank", both have a's; default is false
			filter_startsWith : false,

			// Filter using parsed content for ALL columns
			// be careful on using this on date columns as the date is parsed and stored as time in seconds
			filter_useParsedData : false,

			// data attribute in the header cell that contains the default filter value
			filter_defaultAttrib : 'data-value'

		}

	});

	// External search
	// buttons set up like this:
	// <button type="button" data-filter-column="4" data-filter-text="2?%">Saved Search</button>
	$('button[data-filter-column]').click(function(){
		/*** first method *** data-filter-column="1" data-filter-text="!son"
			add search value to Discount column (zero based index) input */
		var filters = [],
			$t = $(this),
			col = $t.data('filter-column'), // zero-based index
			txt = $t.data('filter-text') || $t.text(); // text to add to filter

		filters[col] = txt;
		// using "table.hasFilters" here to make sure we aren't targetting a sticky header
		$.tablesorter.setFilters( $('#table'), filters, true ); // new v2.9

		/** old method (prior to tablsorter v2.9 ***
		var filters = $('table.tablesorter').find('input.tablesorter-filter');
		filters.val(''); // clear all filters
		filters.eq(col).val(txt).trigger('search', false);
		******/

		/*** second method ***
			this method bypasses the filter inputs, so the "filter_columnFilters"
			option can be set to false (no column filters showing)
		******/
		/*
		var columns = [];
		columns[5] = '2?%'; // or define the array this way [ '', '', '', '', '', '2?%' ]
		$('table').trigger('search', [ columns ]);
		*/

		return false;
	});

});


$(function(){

	// *** widgetfilter_startsWith toggle button ***
	$('button.toggle').click(function(){
		var c = $('#table')[0].config,
		$t = $(this),
		// toggle the boolean
		fsw = !c.widgetOptions.filter_startsWith,
		fic = !c.widgetOptions.filter_ignoreCase;
		if ($t.hasClass('fsw')) {
			c.widgetOptions.filter_startsWith = fsw;
			$('#start').html(fsw.toString());
		} else if ($t.hasClass('fic')) {
			c.widgetOptions.filter_ignoreCase = fic;
			$('#case').html(fic.toString());
		} else {
			$t = c.$headers.eq(1).toggleClass('filter-match');
			$t.find('span').html( $t.hasClass('filter-match') ? '' : ' No' );
		}
		// update search after option change; add false to trigger to skip search delay
		c.$table.trigger('search', false);
		return false;
	});

});
