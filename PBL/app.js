const sidebar = document.getElementById('sidebar')

function toggleSubMenu(button){

  if(!button.nextElementSibling.classList.contains('show')){
    closeAllSubMenus()
  }

  button.nextElementSibling.classList.toggle('show')
  button.classList.toggle('rotate')

  if(sidebar.classList.contains('close')){
    sidebar.classList.toggle('close')
    toggleButton.classList.toggle('rotate')
  }
}

function closeAllSubMenus(){
  Array.from(sidebar.getElementsByClassName('show')).forEach(ul => {
    ul.classList.remove('show')
    ul.previousElementSibling.classList.remove('rotate')
  })
}

//untuk not found
function displayProducts(products) {
  const productContainer = document.querySelector('.container-produk2');
  const notFoundSection = document.querySelector('.notfound');

  // Clear previous products
  productContainer.innerHTML = '';

  if (products.length === 0) {
      notFoundSection.style.display = 'flex'; // Show not found message
  } else {
      notFoundSection.style.display = 'none'; // Hide not found message
      products.forEach(product => {
          // Create product card elements and append to productContainer
          const productCard = document.createElement('div');
          productCard.classList.add('product-card2');
          productCard.innerHTML = `
              <img src="${product.image}" alt="${product.title}" class="product-image2">
              <h2 class="product-title2">${product.title}</h2>
              <p class="product-price2">${product.price}</p>
              <div class="button-group2">
                  <button class="cart-btn2">
                      <img src="img/chart-icon.png" alt="Cart">
                  </button>
                  <button class="details-btn2">Get Details</button>
              </div>
          `;
          productContainer.appendChild(productCard);
      });
  }
}