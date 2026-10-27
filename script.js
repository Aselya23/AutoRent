const cars = [
  {
    name: "Toyota Land Cruiser Prado",
    category: "Кроссовер",
    year: 2021,
    transmission: "Автомат",
    fuel: "Дизель",
    price: 38000
  },
  {
    name: "Toyota Camry",
    category: "Седан",
    year: 2022,
    transmission: "Автомат",
    fuel: "Бензин",
    price: 18000
  },
  {
    name: "Toyota Corolla",
    category: "Седан",
    year: 2021,
    transmission: "Автомат",
    fuel: "Бензин",
    price: 16000
  }
];
const search = document.getElementById("search");
const category = document.getElementById("category");
const maxPrice = document.getElementById("maxPrice");
const sort = document.getElementById("sort");
const carsContainer = document.getElementById("cars");
const count = document.getElementById("count");
// КӨЛІКТЕРДІ КӨРСЕТУ
function renderCars() {
  let result = cars.filter(car => {
    const matchesName =
      car.name.toLowerCase()
        .includes(search.value.toLowerCase());
    const matchesCategory =
      category.value === "all" ||
      car.category === category.value;
    const matchesPrice =
      maxPrice.value === "" ||
      car.price <= Number(maxPrice.value);
    return matchesName &&
           matchesCategory &&
           matchesPrice;
  });
  // Баға бойынша сұрыптау
  result.sort((a, b) => {
    if (sort.value === "asc") {
      return a.price - b.price;
    }
    return b.price - a.price;
  });
  // Көлік саны
  count.textContent =
    `Табылған көлік саны: ${result.length}`;
  // Карточкаларды шығару
  carsContainer.innerHTML = result.map(car => `
    <article class="car-card">
      <div class="car-image">
        🚘
      </div>
      <div class="car-info">
        <span class="badge">
          ${car.category}
        </span>
        <h3>
          ${car.name}
        </h3>
        <p class="details">
          ${car.year} ж. •
          ${car.transmission} •
          ${car.fuel}
        </p>
        <div class="car-bottom">
          <strong>
            ${car.price.toLocaleString("ru-RU")} ₸
            <span class="details">
              / тәулік
            </span>
          </strong>
          <button
            onclick="showDetails('${car.name}')">
            Толығырақ
          </button>
        </div>
      </div>
    </article>
  `).join("");
}
// ТОЛЫҒЫРАҚ
function showDetails(name) {
  const car =
    cars.find(car => car.name === name);
  alert(
    `${car.name}\n` +
    `${car.year} ж.\n` +
    `${car.transmission}\n` +
    `${car.fuel}\n` +
    `${car.price.toLocaleString("ru-RU")} ₸ / тәулік`
  );
}
// MODAL
const modal =
  document.getElementById("modal");
const loginForm =
  document.getElementById("loginForm");
const registerForm =
  document.getElementById("registerForm");
// MODAL АШУ
function openModal(type) {
  modal.style.display = "flex";
  if (type === "login") {
    loginForm.style.display = "block";
    registerForm.style.display = "none";
  } else {
    loginForm.style.display = "none";
    registerForm.style.display = "block";
  }
}
// MODAL ЖАБУ
function closeModal() {
  modal.style.display = "none";
}
// КІРУ
function login() {
  const email =
    document.getElementById("loginEmail").value;
  const password =
    document.getElementById("loginPassword").value;
  if (email === "" || password === "") {
    alert("Барлық өрістерді толтырыңыз!");
    return;
  }
  alert("Сіз жүйеге сәтті кірдіңіз!");
  closeModal();
}
// ТІРКЕЛУ
function register() {
  const name =
    document.getElementById("registerName").value;
  const email =
    document.getElementById("registerEmail").value;
  const password =
    document.getElementById("registerPassword").value;
  if (
    name === "" ||
    email === "" ||
    password === ""
  ) {
    alert("Барлық өрістерді толтырыңыз!");
    return;
  }
  alert("Тіркелу сәтті аяқталды!");
  closeModal();
}
// ФИЛЬТРЛЕР
search.addEventListener(
  "input",
  renderCars
);
category.addEventListener(
  "change",
  renderCars
);
maxPrice.addEventListener(
  "input",
  renderCars
);
sort.addEventListener(
  "change",
  renderCars
);
// БЕТ АШЫЛҒАНДА КӨЛІКТЕРДІ ШЫҒАРУ
renderCars();