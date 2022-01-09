  
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
        console.log("Payment succeeded!");
        break;
      case "processing":
        console.log("Your payment is processing.");
        break;
      case "requires_payment_method":
        console.log("Your payment was not successful, please try again.");
        break;
      default:
        console.log("Something went wrong.");
        break;

    }

  }

  initialize();
  checkStatus();