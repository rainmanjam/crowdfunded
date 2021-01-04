// Tabs for Payment Info
var currentTab = 0; // Current tab is set to be the first tab (0)
// showTab(currentTab); // Display the current tab

$("#payment-form").submit(function(e) {
    e.preventDefault();

    var agree_check = $('input[name="form_check_radio"]:checked').val();

    if (agree_check == 0 || agree_check == "" || !agree_check) {
        alert("You must agree to our TOS to continue.");
        $("#confirmDonation").disable();
    }
});

function showTab(n) {
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";

    if (currentTab == 0) {
        // tab progress effect
        $(".tab-progress .tab-progress-dot").removeClass("active");
        $(".tab-progress .tab-progress-dot").removeClass("activated");
        $(".tab-progress .tab-progress-dot:nth-child(1)").addClass("active");
    } else if (currentTab == 1) {
        $(".tab-progress .tab-progress-dot").removeClass("active");
        $(".tab-progress .tab-progress-dot:nth-child(1)").addClass("activated");
        $(".tab-progress .tab-progress-dot:nth-child(2)").addClass("active");
    } else if (currentTab == 2) {
        $(".tab-progress .tab-progress-dot").removeClass("active");
        $(".tab-progress .tab-progress-dot:nth-child(2)").addClass("activated");
        $(".tab-progress .tab-progress-dot:nth-child(3)").addClass("active");
    }
}

// console.log(currentTab);
function nextPrev(n) {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("tab");
    // Set our form
    var frm_element = document.getElementById("payment-form");

    // Validate third tab
    if (currentTab == 0) {
        x[currentTab].style.display = "none";
        currentTab = currentTab + n;

        showTab(currentTab);
    } else if (currentTab == 1) {

        // form validation
        var fname = frm_element.fname.value;
        var lname = frm_element.lname.value;
        var email_add = frm_element.email.value;

        // If remain_anon isn't checked, and first name is empty
        if (fname == null || fname == "") {
            alert("First name cannot be blank.");
            return false;
        } else if (lname == null || lname == "") {
            alert("Last name cannot be blank.");
            return false;
        } else if (email_add == null || email_add == "") {
            alert("Email cannot be blank");
            return false;
        } else if (
            !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email_add)
        ) {
            console.log(email_add);
            alert("You have entered an invalid email address!");
            return false;
        } else {
            x[currentTab].style.display = "none";
            currentTab = currentTab + n;

            showTab(currentTab);
        }
    } else if (currentTab == 2) {

        x[currentTab].style.display = "none";
        currentTab = currentTab + n;

        showTab(currentTab);
    }
}

function goPrev(n) {
    var x = document.getElementsByClassName("tab");

    x[currentTab].style.display = "none";
    currentTab = currentTab + n;

    // Otherwise, display the correct tab:
    showTab(currentTab);
}

// Calculate total
function calculateTotal() {
    // Get either our token amount from drop down or custom input
    var amountEntered = document.getElementById("token_amount_custom").value;

    // Multiply our tokenAmount x Selected level
    var radios = document.getElementsByName("pledge_level");

    var checkedLevel = "";
    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            var selectedLevel = radios[i].value;

            // only one radio can be logically checked, don't check the rest
            break;
        }
    }

    // Decide what our multiplier is based on selected level
    var multiplyBy = 25;
    if (selectedLevel === "customer") {
        multiplyBy = 25;
    }
    if (selectedLevel === "creator") {
        multiplyBy = 50;
    }
    if (selectedLevel === "developer") {
        multiplyBy = 100;
    }

    var pledgeTotal = amountEntered * multiplyBy;
    // If a default amount has been selected, use that
    if (pledgeTotal > 0 && pledgeTotal < 1000000) {
        // Get our 'amount' element
        var elm = document.getElementById("pledgeAmountTotal");
        var elm2 = document.querySelectorAll(
            ".total_pledge_block>.total_amount"
        );

        // Inject our total into the HTML
        // elm.innerHTML = pledgeTotal;
        for (i = 0; i < elm2.length; i++) elm2[i].innerHTML = pledgeTotal;
    } else {
        alert("Total Pledge should be above 0 and below 1,000,000");
    }
}
