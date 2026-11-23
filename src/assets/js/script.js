document.addEventListener("DOMContentLoaded", function () {
    const department = document.getElementById("department_id");
    const course = document.getElementById("course_id");

    if (!department || !course) return;

    const allCourses = Array.from(course.options).slice(1);

    department.addEventListener("change", function () {
        const selectedDepartment = this.value;

        course.innerHTML = '<option value="">Select Course</option>';

        allCourses.forEach(function (option) {
            if (option.dataset.department === selectedDepartment) {
                course.appendChild(option.cloneNode(true));
            }
        });
    });
});
