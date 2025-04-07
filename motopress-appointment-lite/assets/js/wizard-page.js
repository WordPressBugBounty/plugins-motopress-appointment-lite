(function () {
	'use strict';

	class Wizard {
	  constructor() {
	    this.currentStep = 0;

	    // Get all the necessary elements
	    this.tabs = document.querySelectorAll(".step-tab");
	    this.steps = document.querySelectorAll(".mpa-wizard-step");
	    this.continueButtons = document.querySelectorAll(".next-step");
	    this.previousStepButtons = document.querySelectorAll(".previous-step");
	    this.finishButton = document.querySelector(".finish");
	    this.form = document.querySelector(".mpa-wizard-content form");
	    this.setupEventListeners();
	    this.showStep(this.currentStep);
	  }
	  setupEventListeners() {
	    // Continue buttons event listener
	    this.continueButtons.forEach(button => {
	      button.addEventListener("click", () => {
	        if (this.currentStep < this.steps.length) {
	          this.currentStep++;
	          this.showStep(this.currentStep);
	        }
	      });
	    });

	    // Back buttons event listener
	    this.previousStepButtons.forEach(button => {
	      button.addEventListener("click", () => {
	        if (this.currentStep > 0) {
	          this.currentStep--;
	          this.showStep(this.currentStep);
	        }
	      });
	    });

	    // Tabs buttons event listener
	    this.tabs.forEach((tab, index) => {
	      tab.addEventListener("click", () => {
	        const step = parseInt(tab.getAttribute("data-step"));
	        if (tab.classList.contains("completed") || index > 0 && this.tabs[index - 1].classList.contains("completed")) {
	          this.currentStep = step;
	          this.showStep(step);
	        }
	      });
	    });

	    // Check required fields
	    document.querySelectorAll("input[required]").forEach(input => {
	      input.addEventListener("input", () => {
	        this.checkCompletion(this.currentStep);
	      });
	    });

	    // Finish button event listener
	    if (this.finishButton) {
	      this.finishButton.addEventListener("click", e => {
	        e.preventDefault();
	        this.completeWizard();
	      });
	    }

	    // skip button event listener
	    const skipButton = document.querySelector(".skip-wizard");
	    if (skipButton) {
	      skipButton.addEventListener("click", () => {
	        this.skipWizard();
	      });
	    }
	  }
	  showStep(step) {
	    this.steps.forEach((content, index) => {
	      content.style.display = index === step ? "block" : "none";
	    });
	    this.tabs.forEach((tab, index) => {
	      tab.classList.toggle("active", index === step);
	    });
	  }
	  checkCompletion(step) {
	    const inputs = document.querySelectorAll(`#step-${step} input[required]`);
	    const isFilled = Array.from(inputs).every(input => input.value.trim() !== "");
	    const continueButton = document.querySelector(`#step-${step} .next-step`);
	    if (continueButton) {
	      continueButton.disabled = !isFilled;
	    }
	    if (isFilled) {
	      this.tabs[step].classList.add("completed");
	      this.tabs[step - 1].classList.add("completed");
	    } else {
	      this.tabs[step].classList.remove("completed");
	    }
	  }
	  onCompleteDisableAllSteps() {
	    this.tabs.forEach(tab => {
	      tab.classList.add("disabled", "completed");
	    });
	  }
	  skipWizard() {
	    const nonce = document.querySelector('input[name="mpa_wizard_nonce"]').value;
	    const loader = document.getElementById('wizard-loader');
	    loader.classList.add("show");
	    fetch(ajaxurl, {
	      method: "POST",
	      body: new URLSearchParams({
	        action: "mpa_skip_wizard",
	        mpa_wizard_nonce: nonce
	      })
	    }).then(response => response.json()).then(data => {
	      if (data.success) {
	        window.location.href = data.data.redirect_url;
	      } else {
	        console.log("Error skipping the wizard: " + (data.data?.message || 'Unknown error'));
	      }
	    }).catch(error => console.error("Error:", error));
	  }
	  completeWizard() {
	    const formData = new FormData(this.form);
	    const nonce = document.querySelector('input[name="mpa_wizard_nonce"]').value;
	    const loader = document.getElementById('wizard-loader');
	    const wizardForm = document.getElementById('mpa-wizard-form');
	    const skipButton = document.querySelector('.skip-wizard');
	    const pageLinkParent = document.querySelector('#draft-page-link');
	    const pageLinkElement = document.querySelector('#draft-page-link a');
	    const shouldCreatePageCheckbox = this.form.querySelector('[type="checkbox"][name="should_create_form_page"]');
	    formData.append('mpa_wizard_nonce', nonce);
	    formData.append("action", "mpa_finish_wizard");

	    // need manually append should_create_form_page because checkbox php render has hidden input with same name
	    formData.delete('should_create_form_page');
	    formData.append('should_create_form_page', shouldCreatePageCheckbox.checked);
	    loader.classList.add("show");
	    fetch(ajaxurl, {
	      method: "POST",
	      body: formData
	    }).then(response => response.json()).then(({
	      success,
	      data
	    }) => {
	      if (success) {
	        skipButton.style.display = 'none';
	        this.showStep(this.steps.length - 1);
	        this.onCompleteDisableAllSteps();
	        if (pageLinkElement && data?.form_page) {
	          pageLinkElement.setAttribute('href', data.form_page);
	          pageLinkElement.textContent = data.form_page;
	          pageLinkParent.style.display = 'block';
	        }
	        skipButton.style.display = 'none';
	      } else {
	        throw new Error(data || "Unknown error occurred");
	      }
	    }).catch(error => {
	      console.error("Error:", error);
	      wizardForm.insertAdjacentHTML("afterend", `<p style="color: red; font-weight: bold;">Error: ${error.message}</p>`);
	    }).finally(() => {
	      loader.classList.remove("show");
	    });
	  }
	}

	document.addEventListener("DOMContentLoaded", function () {
	  // Initialize the Wizard when the DOM is ready
	  new Wizard();
	});

})();
