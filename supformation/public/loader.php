<!-- includes/loader.php -->
<div id="loader">
  <div class="loader-content">
    <img src="assets/logo.png" alt="Chargement..." />
    <p>Chargement en cours...</p>
  </div>
</div>

<style>
  /* --- Styles du loader --- */
  #loader {
    position: fixed;
    inset: 0;
    background-color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 99999;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.6s ease, visibility 0.6s ease;
  }

  #loader.hidden {
    opacity: 0;
    visibility: hidden;
  }

  .loader-content {
    text-align: center;
    animation: fadeIn 0.6s ease-in-out;
  }

  #loader img {
    width: 100px;
    height: 100px;
    margin-bottom: 10px;
    animation: spin 2s linear infinite;
  }

  #loader p {
    font-family: "Poppins", sans-serif;
    font-size: 18px;
    color: #333;
    font-weight: 500;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>

<script>
  // Quand la page est complètement chargée
  document.addEventListener("readystatechange", () => {
    if (document.readyState === "complete") {
      const loader = document.getElementById("loader");
      if (loader) {
        loader.classList.add("hidden");
        setTimeout(() => loader.remove(), 600); // suppression après transition
      }
    }
  });
</script>
