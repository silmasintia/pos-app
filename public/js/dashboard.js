document.addEventListener("DOMContentLoaded", function () {
    AOS.init({
        duration: 800,
        easing: "slide",
        once: true,
    });

    new Swiper(".d-slider1", {
        slidesPerView: "auto",
        spaceBetween: 20,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
                spaceBetween: 10,
            },
            480: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            640: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 5,
                spaceBetween: 20,
            },
            1200: {
                slidesPerView: 6,
                spaceBetween: 20,
            },
        },
    });

    fetchSalesChartData();

    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function fetchSalesChartData() {
    fetch("/dashboard/sales-chart-data")
        .then((response) => response.json())
        .then((data) => {
            renderSalesChart(data.labels, data.sales, data.purchases);
        })
        .catch((error) => {
            console.error("Error fetching sales chart data:", error);
        });
}

function renderSalesChart(labels, salesData, purchasesData) {
    const ctx = document.getElementById("salesChart").getContext("2d");

    const salesGradient = ctx.createLinearGradient(0, 0, 0, 150);
    salesGradient.addColorStop(0, "rgba(54, 162, 235, 0.8)");
    salesGradient.addColorStop(1, "rgba(54, 162, 235, 0.1)");

    const purchasesGradient = ctx.createLinearGradient(0, 0, 0, 150);
    purchasesGradient.addColorStop(0, "rgba(255, 99, 132, 0.8)");
    purchasesGradient.addColorStop(1, "rgba(255, 99, 132, 0.1)");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Sales",
                    data: salesData,
                    borderColor: "rgba(54, 162, 235, 1)",
                    backgroundColor: salesGradient,
                    borderWidth: 3,
                    pointBackgroundColor: "rgba(54, 162, 235, 1)",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: "Purchases",
                    data: purchasesData,
                    borderColor: "rgba(255, 99, 132, 1)",
                    backgroundColor: purchasesGradient,
                    borderWidth: 3,
                    pointBackgroundColor: "rgba(255, 99, 132, 1)",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: "top",
                },
                tooltip: {
                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    titleFont: {
                        size: 14,
                        weight: "bold",
                    },
                    bodyFont: {
                        size: 13,
                    },
                    padding: 10,
                    displayColors: true,
                    callbacks: {
                        label: function (context) {
                            return (
                                context.dataset.label +
                                ": Rp " +
                                parseInt(context.parsed.y).toLocaleString(
                                    "id-ID"
                                )
                            );
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(0, 0, 0, 0.05)",
                    },
                    ticks: {
                        callback: function (value) {
                            return (
                                "Rp " + parseInt(value).toLocaleString("id-ID")
                            );
                        },
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                },
            },
        },
    });
}

function formatCurrency(amount) {
    const numericAmount = parseFloat(amount) || 0;
    return "Rp " + Math.round(numericAmount).toLocaleString("id-ID");
}

function showNotification(message, type = "info") {
    const toast = document.createElement("div");
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    const toastContainer = document.createElement("div");
    toastContainer.className =
        "toast-container position-fixed bottom-0 end-0 p-3";
    toastContainer.appendChild(toast);

    document.body.appendChild(toastContainer);

    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000,
    });

    bsToast.show();

    toast.addEventListener("hidden.bs.toast", function () {
        toastContainer.remove();
    });
}
