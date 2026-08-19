function confirmDelete() {
    return confirm("Are you sure you want to delete this record?");
}

document.addEventListener("DOMContentLoaded", function () {

    const bookSearch = document.getElementById("bookSearch");
    const booksTable = document.getElementById("booksTable");

    if (bookSearch && booksTable) {

        const rows = booksTable.querySelectorAll("tbody tr");

        bookSearch.addEventListener("input", function () {

            const searchText = this.value.toLowerCase().trim();

            rows.forEach(function (row) {

                const rowText = row.textContent.toLowerCase();

                row.style.display =
                    rowText.includes(searchText) ? "" : "none";

            });

        });
    }


    const memberSearch = document.getElementById("memberSearch");
    const membersTable = document.getElementById("membersTable");

    if (memberSearch && membersTable) {

        const rows = membersTable.querySelectorAll("tbody tr");

        memberSearch.addEventListener("input", function () {

            const searchText = this.value.toLowerCase().trim();

            rows.forEach(function (row) {

                const rowText = row.textContent.toLowerCase();

                row.style.display =
                    rowText.includes(searchText) ? "" : "none";

            });

        });
    }

});