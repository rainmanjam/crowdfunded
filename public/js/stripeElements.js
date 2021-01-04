// Create a Stripe client.
var stripe = Stripe(stripekey);
var elements = stripe.elements();

// Custom Styling
var style = {
    base: {
        iconColor: '#2c7672',
        border: '#212121',
        color: '#2c7672',
        fontSize: '16px',
        fontSmoothing: 'antialiased',
        ':-webkit-autofill': {
            color: '#2c7672',
        },
        '::placeholder': {
            color: '#2c7672',
        },
    },
    invalid: {
        iconColor: '#FFCC11',
        color: '#FFCC11',
    }
};// Create an instance of the card Element

var card = elements.create("card", {style: style});// Add an instance of the card Element into the `card-element` <div>
card.mount("#card-element");// Handle real-time validation errors from the card Element.

card.addEventListener("change", function(event) {
    var displayError = document.getElementById("card-errors");if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = "";
    }
});// Handle form submission

var form = document.getElementById("payment-form");form.addEventListener("submit", function(event) {
    event.preventDefault();

    stripe.createToken(card).then(function(result) {
        if (result.error) {
            // Inform the user if there was an error
            var errorElement = document.getElementById("card-errors");
            errorElement.textContent = result.error.message;
        } else {

            // Disable submit
            document.getElementById("confirmDonation").disabled = true;
            var elem = document.getElementById("confirmDonation");
            elem.parentNode.removeChild(elem);

            stripeTokenHandler(result.token);
        }
    });
});// Send Stripe Token to Server

function stripeTokenHandler(token) {

    // Disable submit
    document.getElementById("confirmDonation").disabled = true;
    var elem = document.getElementById("confirmDonation");
    elem.parentNode.removeChild(elem);

    // Show processing
    document.getElementById("processingPledge").style.display = "block";

    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById("payment-form");// Add Stripe Token to hidden input
    var hiddenInput = document.createElement("input");
    hiddenInput.setAttribute("type", "hidden");
    hiddenInput.setAttribute("name", "stripeToken");
    hiddenInput.setAttribute("value", token.id);
    form.appendChild(hiddenInput);// Submit form
    form.submit();
}
