function joinProject(projectName) {

    let currentUser = "student1";

    let requests = JSON.parse(localStorage.getItem("requests")) || [];

    // ❌ Check duplicate
    let already = requests.find(r => 
        r.project === projectName && r.user === currentUser
    );

    if (already) {
        alert("Already Requested!");
        return;
    }

    // ✅ Add only once
    requests.push({
        project: projectName,
        user: currentUser,
        status: "Pending"
    });

    localStorage.setItem("requests", JSON.stringify(requests));

    alert("Request Sent!");
}
