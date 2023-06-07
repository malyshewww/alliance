"use strict";

// Drupal.behaviors.afterAjax = {
// 	attach: function (context, settings){
// 		maskInput(context);
// 		popups(context);
// 	}
// };

const up = document.querySelector('.up');

if (up) {
  window.addEventListener('scroll', (e) => {
    if (window.scrollY > 400) {
      up.classList.add('active');
    } else {
      up.classList.remove('active');
    }
  });
  up.addEventListener('click', () => {
    document.querySelector('header').scrollIntoView({block: "start", behavior: "smooth"})
  })
}

const burger = document.querySelector(".burger"),
  mobileMenu = document.querySelector(".mobile-menu");

if (burger) {
  burger.addEventListener("click", () => {
    if (burger.classList.contains("active")) {
      burger.classList.remove("active");
      mobileMenu.classList.remove("active");
      document.querySelector("body").style.overflow = "auto";
    } else {
      burger.classList.add("active");
      mobileMenu.classList.add("active");
      document.querySelector("body").style.overflow = "hidden";
    }
  });
}

const asideBtn = document.querySelector(".menu-button "),
  asideMenu = document.querySelector(".product-list .wrapper aside"),
  asideBtnClose = document.querySelector(".product-list .wrapper aside .close");

if (asideMenu) {
  asideBtn.addEventListener("click", () => {
    asideMenu.classList.add("active");
    asideBtn.classList.add("active");
    document.querySelector("body").style.overflow = "hidden";
  });

  asideBtnClose.addEventListener("click", () => {
    asideMenu.classList.remove("active");
    asideBtn.classList.remove("active");
    document.querySelector("body").style.overflow = "auto";
  });
  window.addEventListener("scroll", () => {
    if (document.querySelector("footer").getBoundingClientRect().y > 600) {
      asideBtn.classList.remove("active");
    } else {
      asideBtn.classList.add("active");
    }
    console.log();
  });
}

document.querySelectorAll('li.expended > a').forEach(item => {
  item.insertAdjacentHTML('beforebegin', `<span>${item.innerHTML}</span>`);
  item.closest('li.expended').querySelector('ul.mobile-submenu').insertAdjacentHTML('afterbegin', `
  <li>
    <a href="${item.getAttribute('href')}">Обзор</a>
  </li>`);
  item.remove();
});

document.querySelectorAll('.mobile-menu a').forEach(item => {
  if (item.innerHTML.toLowerCase().trim() == 'каталог') {
    item.classList.add('cat-mob');
  }
})

const mobMenu = document.querySelectorAll("li.expended > span");

if (mobMenu.length) {
  function openAcc(item) {
    item.classList.add("active");
    let panel = item.nextElementSibling;
    panel.style.paddingTop = "24px";
    panel.style.maxHeight = panel.scrollHeight + 48 + "px";
    if (panel.classList.contains("mobile-subsubmenu")) {
      panel.closest(".mobile-submenu").style.maxHeight =
        parseInt(panel.closest(".mobile-submenu").style.maxHeight) +
        panel.scrollHeight +
        "px";
    }
  }

  function closeAcc(item) {
    item.classList.remove("active");
    let panel = item.nextElementSibling;
    panel.style.paddingTop = "0px";
    panel.style.maxHeight = null;
  }

  mobMenu.forEach((item) => {
    item.addEventListener("click", () => {
      if (item.classList.contains("active")) {
        closeAcc(item);
      } else {
        openAcc(item);
      }
    });
  });
}

const prodGoTo = document.querySelector(".product-page__goto");

if (prodGoTo) {
  prodGoTo.addEventListener("click", () => {
    document
      .querySelector(".product-page__mods")
      .scrollIntoView({ block: "start", behavior: "smooth" });
  });
}

// Аккордеоны товара
const faqItemsProd = document.querySelectorAll(".acc-title ");

if (faqItemsProd.length) {
  function openAcc(item) {
    item.classList.add("active");
    let panel = item.nextElementSibling;
    panel.style.paddingTop = "28px";
    panel.style.maxHeight = panel.scrollHeight + 28 + "px";
  }

  function closeAcc(item) {
    item.classList.remove("active");
    let panel = item.nextElementSibling;
    panel.style.paddingTop = "0px";
    panel.style.maxHeight = null;
  }

  openAcc(faqItemsProd[0]);

  faqItemsProd.forEach((item) => {
    item.addEventListener("click", () => {
      if (item.classList.contains("active")) {
        closeAcc(item);
      } else {
        openAcc(item);
      }
    });
  });
}

const tabsHead = document.querySelectorAll(".tabs-heads li a"),
  tabsBodyes = document.querySelectorAll(".tabs-body");

if (tabsHead.length) {
  function openTab(i) {
    tabsHead[i].classList.add("active");
    tabsBodyes[i].classList.add("active");
    tabsBodyes[i].style.maxHeight = tabsBodyes[i].scrollHeight + "px";
  }
  function closeTab(i) {
    tabsHead[i].classList.remove("active");
    tabsBodyes[i].classList.remove("active");
    tabsBodyes[i].style.maxHeight = null;
  }

  function closeAllTabs() {
    for (let i = 0; i < tabsHead.length; i++) {
      closeTab(i);
    }
  }

  tabsHead.forEach((item, i) => {
    item.addEventListener("click", (e) => {
      e.preventDefault("");
      if (item.classList.contains("active")) {
        closeTab(i);
      } else {
        closeAllTabs();
        openTab(i);
      }
    });
  });
}

// Линия прокрутки страницы в карточке товаров
const underline = document.querySelector(".product-page__nav span"),
  stickyItems = document.querySelectorAll(".product-page__nav  ul li"),
  scrollBlocks = document.querySelectorAll("[theView='']");

if (underline) {
  function goToItem(i) {
    underline.style.transform = `translateX(${i.getBoundingClientRect().x}px)`;
    underline.style.width = i.getBoundingClientRect().width + "px";
  }

  goToItem(stickyItems[0]);

  stickyItems.forEach((item) => {
    item.addEventListener("mouseover", () => {
      goToItem(item);
    });
  });

  stickyItems.forEach((item) => {
    item.addEventListener("click", () => {
      let target = document.querySelector(item.getAttribute("toView"));
      target.scrollIntoView({ block: "start", behavior: "smooth" });
    });
  });

  stickyItems.forEach((item) => {
    item.addEventListener("mouseleave", () => {
      stickyItems.forEach((jtem) => {
        if (jtem.classList.contains("active")) {
          goToItem(jtem);
        }
      });
    });
  });

  window.addEventListener("scroll", () => {
    // Создаётся массив положения всех вершин блоков
    let arr = [];
    scrollBlocks.forEach((item) => {
      arr.push(item.getBoundingClientRect().top + 20);
    });
    // Если число положительное, то блок активируется
    for (let i = 0; i < scrollBlocks.length; i++) {
      if (arr[i] > 0) {
        stickyItems.forEach((jtem) => {
          jtem.classList.remove("active");
        });
        stickyItems[i].classList.add("active");
        goToItem(stickyItems[i]);
        break;
      }
    }
  });
}

// Аккордеоны
const faqItems = document.querySelectorAll(".FAQ__item-title");

if (faqItems.length) {
  function openAcc(item) {
    item.classList.add("active");
    let panel = item.nextElementSibling;
    panel.style.maxHeight = panel.scrollHeight + "px";
    item.closest(".FAQ__item").style.paddingBottom = "30px";
  }

  function closeAcc(item) {
    item.classList.remove("active");
    let panel = item.nextElementSibling;
    panel.style.maxHeight = null;
    item.closest(".FAQ__item").style.paddingBottom = "0px";
  }

  openAcc(faqItems[0]);

  faqItems.forEach((item) => {
    item.addEventListener("click", () => {
      if (item.classList.contains("active")) {
        closeAcc(item);
      } else {
        let openItem = document.querySelector(".FAQ__item-title.active");
        if (openItem) {
          closeAcc(openItem);
        }
        openAcc(item);
      }
    });
  });
}

if (document.querySelector(".home-promo a.btn")) {
  document.querySelector(".home-promo a.btn").addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelector(".home-calc")
      .scrollIntoView({ block: "start", behavior: "smooth" });
  });
}

if (document.querySelector(".service-cart__top a.btn")) {
  document
    .querySelector(".service-cart__top a.btn")
    .addEventListener("click", (e) => {
      e.preventDefault();
      document
        .querySelector(".home-calc")
        .scrollIntoView({ block: "start", behavior: "smooth" });
    });
}

if (document.querySelector(".home-info a.btn")) {
  document.querySelector(".home-info a.btn").addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelector(".home-calc")
      .scrollIntoView({ block: "start", behavior: "smooth" });
  });
}

// ЗАГЛУШКА ДЛЯ КЛИЕНТА, ПРИ ПЕРЕНОСЕ УДАЛИТЬ!!!
// ЗАГЛУШКА ДЛЯ КЛИЕНТА, ПРИ ПЕРЕНОСЕ УДАЛИТЬ!!!

// Смена активных пунктов хедера
const headerTabs = document.querySelectorAll(".header__top nav ul li a");

if (document.querySelector("body").classList.contains("front-copy")) {
  headerTabs[0].classList.remove("active");
  headerTabs[1].classList.add("active");
  document.querySelector(".header__subnav ul").insertAdjacentHTML(
    "beforeend",
    `
    <li><a href="#">Ответ-вопрос</a></li>
  `
  );
}

// Снятие фильтров
const clearAllCheck = document.querySelector(
  ".product-list form .form-item.clearAll input"
);
if (clearAllCheck) {
  clearAllCheck.addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelectorAll(".product-list form .form-item div input")
      .forEach((item) => {
        item.checked = false;
      });
  });
}

// ЗАГЛУШКА ДЛЯ КЛИЕНТА, ПРИ ПЕРЕНОСЕ УДАЛИТЬ!!!
// ЗАГЛУШКА ДЛЯ КЛИЕНТА, ПРИ ПЕРЕНОСЕ УДАЛИТЬ!!!

// Окна
function popups(elem = document) {
  // Открыть
  let buttonOpenPopup = elem.querySelectorAll("[data-popup]");
  if (buttonOpenPopup) {
    buttonOpenPopup.forEach(function (item) {
      item.addEventListener("click", function (e) {
        e.preventDefault();
        let popup_id = item.getAttribute("data-popup");
        elem.querySelector("#" + popup_id + "").classList.add("active");
      });
    });
  }
  // Закрыть
  let popup = elem.querySelectorAll(".popup");
  if (popup) {
    popup.forEach(function (item) {
      item.addEventListener("click", function (e) {
        if (e.target.matches(".popup") || e.target.matches(".popup__close")) {
          item.classList.remove("active");
        }
      });
    });
  }
}
popups();

// Маска телефона
function maskPhone(elem = document) {
  let inputs = elem.querySelectorAll('input[type="tel"]');
  if (inputs) {
    inputs.forEach((phone) => {
      let code = "+7",
        find = /\+7/;
      code = "+7";
      find = /\+7/;
      phone.addEventListener("focus", function () {
        phone.value = code + " " + phone.value.replace(code + " ", "");
      });
      phone.addEventListener("input", function () {
        let val = phone.value.replace(find, ""),
          res = code + " ";
        val = val.replace(/[^0-9]/g, "");
        for (let i = 0; i < val.length; i++) {
          res += i === 0 ? " (" : "";
          res += i == 3 ? ") " : "";
          res += i == 6 || i == 8 ? "-" : "";
          if (i == 10) break;
          res += val[i];
        }
        phone.value = res;
      });
      phone.addEventListener("blur", function () {
        let val = phone.value.replace(find, "");
        val = val.trim();
        if (!val) phone.value = null;
      });
    });
  }
}
maskPhone();

Fancybox.bind("[data-fancybox]", {
  placeFocusBack: false, // Починить баг с фенсибоксом и свипером
});

const projectSwiper = document.querySelector(".project-item__swiper");
if (projectSwiper) {
  const projectSwiperBody = new Swiper(projectSwiper, {
    loop: true,
    centeredSlides: "auto",
    spaceBetween: 20,
    speed: 12000,
    loopedSlides: 12,
    autoplay: {
      delay: 1,
      disableOnInteraction: false,
    },
    slidesPerView: "auto",
    allowTouchMove: false,
  });
}

const aboutMarquee = document.querySelector(".about__marquee-swiper");
if (aboutMarquee) {
  const aboutMarqueeBody = new Swiper(aboutMarquee, {
    loop: true,
    centeredSlides: "auto",
    spaceBetween: 19,
    speed: 12000,
    loopedSlides: 14,
    autoplay: {
      delay: 1,
      disableOnInteraction: false,
    },
    slidesPerView: "auto",
    allowTouchMove: false,
  });
}

const aboutGall = document.querySelector(".about__gall-swiper");
if (aboutGall) {
  const aboutGallBody = new Swiper(aboutGall, {
    spaceBetween: 10,
    speed: 600,
    slidesPerView: "auto",
    navigation: {
      nextEl: ".gall-next",
      prevEl: ".gall-prev",
    },
  });
}

const serviceSlide = document.querySelector(".service-cart__swiper");

if (serviceSlide) {
  const serviceSlideBody = new Swiper(serviceSlide, {
    slidesPerView: "auto",
    spaceBetween: 15,
    freeMode: true,
    scrollbar: {
      el: ".swiper-scrollbar",
      dragSize: 353,
    },
    breakpoints: {
      700: {
        spaceBetween: 30,
      },
    },
  });

  if (window.screen.width > 1000) {
    let slidesNum = serviceSlideBody.slides.length,
      overlay = document.querySelector(".service-cart__how .overlay"),
      overlayWidth = overlay.clientWidth,
      segmentWidth = overlayWidth / slidesNum,
      overlayMouse = document.querySelector(".overlay__mouse");

    const segmentIndex = [];

    for (let i = 0; i < slidesNum; i++) {
      segmentIndex.push(i * segmentWidth);
    }

    function goToSlide(ind) {
      serviceSlideBody.slideTo(ind, 800);
    }

    function findSegment(x) {
      for (let i = 0; i < slidesNum; i++) {
        if (x < segmentIndex[i]) {
          return i - 1;
        }
      }
      return slidesNum - 1;
    }

    overlay.addEventListener("mousemove", (e) => {
      goToSlide(findSegment(e.clientX));
      overlayMouse.classList.add("active");
      overlayMouse.style.left = e.clientX + "px";
    });
    overlay.addEventListener("mouseleave", (e) => {
      overlayMouse.classList.remove("active");
    });
  }
}

const productSwiper = document.querySelector(".product-page__swiper");
const productSubSwiper = document.querySelector(".product-page__subswiper");

if (productSwiper) {
  const productSubSwiperBody = new Swiper(productSubSwiper, {
    spaceBetween: 4,
    slidesPerView: "auto",
    direction: "horizontal",
    navigation: {
      nextEl: ".sub-next",
      prevEl: ".sub-prev",
    },
    breakpoints: {
      1100: {
        direction: "vertical",
      },
    },
  });

  const productSwiperBody = new Swiper(productSwiper, {
    slidesPerView: 1,
    direction: "horizontal",
    thumbs: {
      swiper: productSubSwiperBody,
    },
  });
}

// Обенуть таблицы
if (window.innerWidth < 900) {
  let contentTable = document.querySelectorAll(".content table");
  if (contentTable) {
    contentTable.forEach(function (item) {
      let tableWrap = document.createElement("div");
      tableWrap.setAttribute("class", "table-wrap");
      item.parentNode.insertBefore(tableWrap, item);
      tableWrap.appendChild(item);
    });
  }
}

const productFilters = document.querySelectorAll(
  ".product-list form .form-item"
);
if (productFilters.length) {
  productFilters.forEach((item) => {
    let filters = item.querySelectorAll("div");
    if (filters.length > 6) {
      let num = filters.length - 6;
      for (let i = 6; i < filters.length; i++) {
        filters[i].style.display = "none";
      }
      item.insertAdjacentHTML(
        "beforeend",
        `
			<span class="showAll">Ещё ${num}</span>
		`
      );
      let showall = item.querySelector(".showAll");
      showall.addEventListener("click", () => {
        for (let i = 6; i < filters.length; i++) {
          filters[i].style.display = "flex";
        }
        showall.remove();
      });
    }
  });
}

const homeText = document.querySelector(".home-info__text");

if (homeText) {
  if (homeText.clientHeight > 130) {
    homeText.classList.add("hidden");
    homeText.insertAdjacentHTML(
      "afterend",
      `
      <span class="showAll">Читать далее</span>
    `
    );
    let showAllMain = document.querySelector(".showAll");
    showAllMain.addEventListener("click", () => {
      homeText.classList.remove("hidden");
      showAllMain.remove();
    });
  }
}

const faqBtn = document.querySelector(".FAQ__top a.btn");

if (faqBtn) {
  let faqBtnFixed = document.querySelector(".FAQ__fixed");
  window.addEventListener("scroll", () => {
    console.log(document.querySelector(".footer").getBoundingClientRect());
    if (
      faqBtn.getBoundingClientRect().top < 0 &&
      document.querySelector(".footer").getBoundingClientRect().top > 700
    ) {
      faqBtnFixed.classList.add("active");
    } else {
      faqBtnFixed.classList.remove("active");
    }
  });
}
const mapElem = document.getElementById("map");
// Карта на странице контактов
if (mapElem) {
  let isLoaded = false;
  function loadMap() {
    var script = document.createElement("script");
    script.src = "https://api-maps.yandex.ru/2.1/?lang=ru_RU";
    document.body.appendChild(script);
    isLoaded = true;
    script.onload = function () {
      ymaps.ready(function () {
        // Инициализация карты

        let mapContacts = new ymaps.Map(
          mapElem,
          {
            zoom: 14,
            center: [56.247966, 43.936062],
            controls: ["zoomControl"],
          },
          {
            searchControlProvider: "yandex#search",
          }
        );

        // Основная метка
        let mainIco1 = new ymaps.Placemark(
          [56.247966, 43.936062],
          { hintContent: "ООО «ЭффектЭнергоМонтаж»" },
          {
            iconLayout: "default#image",
            iconImageHref: "/themes/alliance/dist/img/icons/map-pin.svg",
            iconImageSize: [50, 56],
            iconImageOffset: [-25, -56],
          }
        );

        mapContacts.geoObjects.add(mainIco1);
        mapContacts.behaviors.disable("scrollZoom");
      });
    };
  }
  if(mapElem.getBoundingClientRect().top < window.innerHeight){
    loadMap();
  }
  window.addEventListener('scroll', function () {
    if(!isLoaded && mapElem.getBoundingClientRect().top < window.innerHeight){
      loadMap();
    }
  });
}
var form = document.querySelectorAll('.modal-form');

if(form) {
  form.forEach(function (item) {
    item.onsubmit = function(event){
      var xhr = new XMLHttpRequest();
      var formData = new FormData(item);


      fetch('/session/token').then(function (response) {
        return response.text();
      }).then(function (token) {

        //open the request
        xhr.open('POST','/webform_rest/submit?_format_json')
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.setRequestHeader("X-CSRF-Token", token);
        //send the form data
        xhr.send(JSON.stringify(Object.fromEntries(formData)));

        xhr.onreadystatechange = function() {
          if (xhr.readyState == XMLHttpRequest.DONE) {

            item.reset(); //reset form after AJAX success or do something else
            if(xhr.status == 200) {
//              item.closest('.wrap_form').style.display = 'none';
//              item.closest('.wrap_form').nextElementSibling.style.display = 'initial';
              document.querySelector("#thanks").classList.add("active");

            }
          }
        }
      });
      //Fail the onsubmit to avoid page refresh.
      return false;
    }
  });
}
