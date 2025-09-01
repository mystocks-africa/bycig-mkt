package main

import (
	"fmt"
	"os"
)

func main() {
	var email string 
	var role string 

	fmt.Println("Enter your email:")
	fmt.Scan(&email)

	fmt.Println("Enter your role:")
	fmt.Scan(&role)

	if (role != "cluster_leader" && role != "user") {
		fmt.Println("Invalid role. It is either 'cluster_leader' or 'user'")
		os.Exit(0)
	}

	fmt.Println("Thank you!")

	// Temporary debugging
	fmt.Println(email, "and", role)
}
