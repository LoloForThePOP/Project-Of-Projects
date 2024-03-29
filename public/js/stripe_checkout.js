  
$(document).ready(function(){
  
  // stripe public API key.

  const stripe = Stripe(stripePublicKey);
  
  function initialize() {

    document
      .querySelector("#payment-form")
      .addEventListener("submit", handleSubmit);

    // Fetches a payment intent and captures the client secret
    elements = stripe.elements({ clientSecret });
    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");

  }

  async function handleSubmit(e) {

    e.preventDefault();

    const { error } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: redirectAfterSuccessURL
      },
    })

    if (error.type === "card_error" || error.type === "validation_error") {

      showMessage(error.message);
  
    } else {
  
      showMessage("An unexpected error occured.");
  
    }
  
  
    setLoading(false);

  }

  // Fetches the payment intent status after payment submission

  async function checkStatus() {

    const clientSecret = new URLSearchParams(window.location.search).get(
      "payment_intent_client_secret"
    );

    if (!clientSecret) {
      return;
    }

    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

    switch (paymentIntent.status) {

      case "succeeded":
        console.log("Paiement réussi !");
        break;
      case "processing":
        console.log("Votre paiement est en cours de traitement.");
        break;
      case "requires_payment_method":
        console.log("Une erreur est survenue. Votre paiement n'est pas réussi, merci de réessayer.");
        break;
      default:
        console.log("Une erreur est survenue.");
        break;

    }

  }

  initialize();
  checkStatus();



// ------- UI helpers -------

function showMessage(messageText) {

  const messageContainer = document.querySelector("#payment-message");

  messageContainer.classList.remove("hidden");

  messageContainer.textContent = messageText;

  setTimeout(function () {

    messageContainer.classList.add("hidden");

    messageText.textContent = "";

  }, 4000);

}


// Show a spinner on payment submission

function setLoading(isLoading) {

  if (isLoading) {

    // Disable the button and show a spinner

    document.querySelector("#submit").disabled = true;

    document.querySelector("#spinner").classList.remove("hidden");

    document.querySelector("#button-text").classList.add("hidden");

  } else {

    document.querySelector("#submit").disabled = false;

    document.querySelector("#spinner").classList.add("hidden");

    document.querySelector("#button-text").classList.remove("hidden");

  }

}

});