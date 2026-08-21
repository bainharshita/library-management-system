function confirmDelete() {
    return confirm("Are you sure you want to delete this book?");
}

document.addEventListener("DOMContentLoaded", function () {

    /* BOOK SEARCH */

    const bookSearch = document.getElementById("bookSearch");
    const booksTable = document.getElementById("booksTable");

    if (bookSearch && booksTable) {

        const rows = booksTable.querySelectorAll("tbody tr");

        bookSearch.addEventListener("input", function () {

            const searchText = this.value.toLowerCase().trim();

            rows.forEach(function (row) {

                const rowText = row.textContent.toLowerCase();

                if (rowText.includes(searchText)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }

            });

        });

    }


    /* MEMBER SEARCH */

    const memberSearch = document.getElementById("memberSearch");
    const membersTable = document.getElementById("membersTable");

    if (memberSearch && membersTable) {

        const rows = membersTable.querySelectorAll("tbody tr");

        memberSearch.addEventListener("input", function () {

            const searchText = this.value.toLowerCase().trim();

            rows.forEach(function (row) {

                const rowText = row.textContent.toLowerCase();

                if (rowText.includes(searchText)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }

            });

        });

    }

});