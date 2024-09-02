<?php
// Products page MK
add_shortcode("products_tiles", "prod_filter_shortcode");
function prod_filter_shortcode()
{
    ob_start(); // Start output buffering

    echo '<div class="blog-grid"></div>';
    echo '<div class="blog-pagination"></div>';

    return ob_get_clean(); // Return the buffered output
}

// Filters Shortcode
function custom_range_filter_form()
{
    global $product; ?>

	<div class="accordion">
        <div class="accordion-item">
            <button class="accordion-header">Select Wavelength</button>
            <div class="accordion-content"> <!-- First filter -->
                <div class="product_range">
					<form method="GET" action="">
						<div class="wave_radio">
							<div class=""><input type="radio" name="range_filter" id="eb_range" /><span>Search by range</span></div>
							<div class=""><input type="radio" name="range_filter" id="eb_customText" /><span>Search by custom value</span></div>
						</div>
				<!-- 	Min & Max range boxes	 -->
						<div class="eb-flex">
							<input type="number" name="min_value" id="min_value" placeholder="Min" />
							<input type="number" name="max_value" id="max_value" placeholder="Max" />
						</div>
				<!-- 	Input Text Box	 -->
						<div>
							<input type="number" name="custom_value" id="custom_value" placeholder="Enter wavelength value" />
						</div>
					</form>
				</div>
            </div>
        </div>
        <div class="accordion-item">
            <button class="accordion-header">Application</button>
            <div class="accordion-content"> <!-- Second Filter -->
                <?php $products = get_terms([
                    "taxonomy" => "pa_application",
                    "hide_empty" => false,
                ]); ?>
				<div class="application_dd">
					<?php foreach ($products as $product) {
         echo '<span><input type="checkbox" value="' .
             $product->name .
             '" name="app_cbx" /><span>' .
             $product->name .
             "</span></span>";
     } ?>
					
				</div>
            </div>
        </div>
        
    </div>

    <?php
}
add_shortcode("eb_range_filters", "custom_range_filter_form", 20);

// AJAX filtering for products
function eb_filter_posts_function()
{
    $filterVal = isset($_POST["filters"]) ? $_POST["filters"] : "";
    $search_query = isset($_POST["search"])
        ? sanitize_text_field($_POST["search"])
        : "";
    $page = isset($_POST["page"]) ? absint($_POST["page"]) : 1;

    $args = [
        "post_type" => "product",
        "post_status" => "publish",
        "posts_per_page" => 6,
        "paged" => $page,
        "order" => "DESC",
    ];

    if (!empty($filterVal) && $filterVal !== "") {
        // 		In Case of custom value Start
        if (!empty($filterVal["custom"][0])) {
            $minArray = get_range($filterVal["custom"][0]);
            $maxArray = get_range($filterVal["custom"][0]);
            // 			$minArray = get_min_range($filterVal['custom'][0]);
            // 			$maxArray = get_max_range($filterVal['custom'][0]);
        } else {
            $minArray = $filterVal["min"][0];
            $maxArray = $filterVal["max"][0];
        }

        // 		In Case of custom value Ends

        $relation = "OR";
        $filterltn = "OR";
        if (!empty($filterVal["min"][0]) && !empty($filterVal["max"][0])) {
            $filterltn = "AND";
        }

        if ( (!empty($filterVal["min"][0]) || !empty($filterVal["max"][0]) || !empty($filterVal["custom"][0]) ) && !empty($filterVal["application"]) ) {
            $relation = "AND";
        }

        $args["tax_query"] = [
            "relation" => $relation,
            [
                "relation" => $filterltn,
                [
                    "taxonomy" => "pa_minimum-wavelength",
                    "terms" => $minArray,
                    "field" => "name",
					"compare" => ">=",
                    //"type" => "NUMERIC"
                ],
                [
                    "taxonomy" => "pa_maximum-wavelength",
                    "terms" => $maxArray,
                    "field" => "name",
					"compare" => "<=",
                    //"type" => "NUMERIC"
                ],
            ],
            [
                "taxonomy" => "pa_application",
                "terms" => $filterVal["application"],
                "field" => "name",
            ],
        ];
    }

    $result = new WP_Query($args);

    $html = "";
	//$html = print_r ($args);
    $html .= '<div class="product_div">';
    if ($result->have_posts()) {
        while ($result->have_posts()) {
            $result->the_post();
            $productimg = get_the_post_thumbnail_url(get_the_ID());
            $minLength = get_the_terms(get_the_ID(), "pa_minimum-wavelength");
            $maxLength = get_the_terms(get_the_ID(), "pa_maximum-wavelength");
            $appName = wp_get_post_terms(get_the_ID(), "pa_application", [
                "fields" => "names",
            ]);

            $html .= '<div class="product_tile">';
            $html .=
                '<div class="product_img">
							<img src="' .
                $productimg .
                '" />
							<span>Min. Wavelength: ' .
                $minLength[0]->name .
                '</span>
							<span>Max. Wavelength: ' .
                $maxLength[0]->name .
                "</span>";

            $html .= "</div>";
            $html .= '<div class="product_desc">';
            $html .=
                '<div class="product_title"><a href="' .
                get_the_permalink() .
                '">' .
                get_the_title() .
                '</a></div>
							  <div class="product_exc">';
            foreach ($appName as $term):
                $html .= "<span>" . $term . "</span>";
            endforeach;

            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }
    } else {
        $html .= "<h2>Oops! No products found.</h2>";
    }
    $html .= "</div>";

    // Collect pagination links
    $pagination = get_pagination_links($result->max_num_pages, $page);

    // Send JSON response with posts and pagination
    $response = [
        "posts" => $html,
        "pagination" => $pagination,
        "filter" => $filterVal,
    ];

    wp_send_json($response); // Send response and exit
}

add_action("wp_ajax_eb_filter_posts", "eb_filter_posts_function");
add_action("wp_ajax_nopriv_eb_filter_posts", "eb_filter_posts_function");

// GET Range
function get_range($val)
{
    $customVal = $val;
    $range = 20;

    // Create an array with values below and above the center value
    $newArray = range($customVal - $range, $customVal + $range);

    // Output the resulting array
    return $newArray;
}

function get_min_range($val)
{
    $customVal = $val;
    $range = 20;

    // Create an array with values below and above the center value
    $newArray = range($customVal - $range, $customVal);

    // Output the resulting array
    return $newArray;
}
function get_max_range($val)
{
    $customVal = $val;
    $range = 20;

    // Create an array with values below and above the center value
    $newArray = range($customVal + $range, $customVal);

    // Output the resulting array
    return $newArray;
}

// Function to get pagination links for AJAX
function get_pagination_links($total_pages, $current_page)
{
    if ($total_pages <= 1) {
        return ""; // No pagination required if only one page
    }

    $pagination =
        '<div class="pagination" style="display: flex; justify-content: center; grid-gap: 5px; align-items: center">';

    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = $i === $current_page ? " active" : "";
        $pagination .=
            '<span class="page-numbers' .
            $active_class .
            '" data-page="' .
            $i .
            '">' .
            $i .
            "</span>";
    }

    $pagination .= "</div>";
    return $pagination;
}
?>

<!-- JS CODE -->
//Code to perform AJAX loading of products Starts here
jQuery(function ($) {

    var filterVal = ''; // Default value for filters
    var currentPage = 1;
//     var searchValue = ''; // Initialize search value
	var getUrl = window.location;
   	var baseUrl = getUrl.protocol + "//" + getUrl.host + '/wp-admin/admin-ajax.php';

    function loadPosts(page, filterVal) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'eb_filter_posts',
                filters: filterVal,
                page: page,
//                 search: searchQuery,
            },
			beforeSend: function(){
				$(document).find('.blog-grid').html('<div class="ebloader" style="text-align:center;"><img src="/wp-content/uploads/2024/08/Glass-spheres.gif"><p>Please wait</p></div>');
				$(document).find('.blog-pagination').html(''); 
				
			},
            success: function (response) { 
                console.log(response);
                $(document).find('.blog-grid').html(response.posts); 
                $(document).find('.blog-pagination').html(response.pagination); 
                
            },
            error: function (error) {
                console.error("Error:", error.responseText);
            },
        });
    }

    $('#apply_filters').on('click', function () {
        var minValue = $(document).find('.product_range input#min_value').val(),
			maxValue = $(document).find('.product_range input#max_value').val(),
			cstValue = $(document).find('.product_range input#custom_value').val(),
			minArray = [],
			maxArray = [],
			cstArray = [],
			appArray = [];
		$(document).find('.application_dd input[name=app_cbx]:checked').each(function() {
            appArray.push($(this).val());
        });
		if(parseInt(minValue) >= parseInt(maxValue)){
			return false;
		}
		minArray.push(minValue);
		maxArray.push(maxValue);
		cstArray.push(cstValue);
		var valArray = {
			'min': minArray,
			'max': maxArray,
			'custom': cstArray,
			'application': appArray
		}

		currentPage = 1; 
		if ((valArray['min'][0].length === 0) && (valArray['max'][0].length === 0) && (valArray['custom'][0].length === 0) && (valArray['application'].length === 0)) {
			loadPosts(currentPage, filterVal); 
		}else{
			loadPosts(currentPage, valArray);
		}
        
        
    });

//     $('#search').on('input', function () {
//         searchValue = $(this).val();
//         currentPage = 1;
//         loadPosts(currentPage, searchValue);
//     });

    $('.blog-pagination').on('click', '.page-numbers', function () {
        var page = $(this).data('page');
        currentPage = page;
//         loadPosts(currentPage, filterVal);
     
		var minValue = $(document).find('.product_range input#min_value').val(),
			maxValue = $(document).find('.product_range input#max_value').val(),
			cstValue = $(document).find('.product_range input#custom_value').val(),
			minArray = [],
			maxArray = [],
			cstArray = [],
			appArray = [];
		$(document).find('.application_dd input[name=app_cbx]:checked').each(function() {
            appArray.push($(this).val());
        });
		if(parseInt(minValue) >= parseInt(maxValue)){
			return false;
		}
		minArray.push(minValue);
		maxArray.push(maxValue);
		cstArray.push(cstValue);
		var valArray = {
			'min': minArray,
			'max': maxArray,
			'custom': cstArray,
			'application': appArray
		}

// 		currentPage = 1; 
		if ((valArray['min'][0].length === 0) && (valArray['max'][0].length === 0) && (valArray['custom'][0].length === 0) && (valArray['application'].length === 0)) {
			loadPosts(currentPage, filterVal); 
		}else{
			loadPosts(currentPage, valArray);
		}
        
    });

    // Initial load for "All" category
    loadPosts(currentPage, filterVal); 
	
	// Input fields validation for Min and Max value
// 	$('#min_value').on('input', function(){
// 		var minval = $(this).val();
// 		console.log(minval);
// 	});
	
// 	Clear filters
	$('#clear_filters').on('click',function(){
		$(document).find('.product_range input#min_value').val(''),
		$(document).find('.product_range input#max_value').val(''),
		$(document).find('.product_range input#custom_value').val(''),
		$(document).find('.application_dd input[name=app_cbx]').prop("checked", false);
		loadPosts(currentPage, filterVal);
	});
});
