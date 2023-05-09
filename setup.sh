# This file contains the code used for the setup script
# checks for sudo privileges before executing the script
if [ "$USER" != "root" ]; then
    printf "This script must be executed with sudo privileges.\n\nAborted.\n"
    exit 2
fi

# FUNCTIONS
# reads a line of the ini file
read_ini_file() {
    # Index
    # $1 -> group
    # $2 -> variable
    value=$(sed -nr "/^\[$1\]/ { :l /^$2[ ]*=/ { s/[^=]*=[ ]*//; p; q;}; n; b l;}" ./app.ini)
    echo $value
}
# used to edit a line in the ini file
edit_ini_line() {
    # Index
    # $1 -> group
    # $2 -> variable
    # $3 -> new value

    # replace variable ($2) in group ($1) with value ($3)
    sed -i.bak '/^\['$1']/,/^\[/{s/^'$2'[[:space:]]*=.*/'$2' = '$3'/}' app.ini
}
# used to present a 'Y/N' query to the terminal
yes_no_query() {
    while true; do
        read -p "$* [Y/N]: " yn
        case $yn in
            [Yy]*) return 0  ;;  
            [Nn]*) return 1  ;;
        esac
    done
}

# asks the user a query and edits the ini file to change a respective variable
standard_input_query() {
    # Index
    # $1 -> question
    # $2 -> group
    # $3 -> variable
    # $4 -> default
    # $5 -> input nature ['n' -> numerical, 's' -> string]

    # while the input is not valid, request input
    valid_input=0
    until [ $valid_input -eq 1 ]
    do

        # ask the user the question and 
        read -p "$1"": " user_input

        # the users input was empty
        if [ -z "$user_input" ]; then
            # if the input cannot be empty (the default is empty)
            if [ -z "$4" ]; then
                echo "ALERT: This input cannot be empty, please try again."
                echo ""
            # if the input can be empty (the default is not empty)
            else
                # set the users input to the default
                finalised_input=$4
                valid_input=1
            fi
        # the users input was not empty
        else
            # set the user input to the users input
            finalised_input=$user_input
            valid_input=1
        fi

    done

    # edits the ini file to change the given variable to the users input
    edit_ini_line $2 $3 $finalised_input
}

# VARIABLES
settings_configured=$(read_ini_file "setup_settings" "settings_configured")
sys_admin_configured=$(read_ini_file "setup_settings" "sys_admin_configured")
database_settings_configured=$(read_ini_file "setup_settings" "database_settings_configured")
database_created=$(read_ini_file "setup_settings" "database_created")

# MAIN
printf "%60s" " " | tr ' ' '#'  && printf "\n"
printf "Welcome to the doyrms-registration setup script\n-\n"
# status message for settings
if [ $settings_configured -eq 0 ]; then
    printf "\n  [FALSE] Main settings have been configured"
elif [ $settings_configured -eq 1 ]; then
    printf "\n  [TRUE]  Main settings have been configured"
else
    printf "\n  [N/A]   Main settings have been configured"
fi
# status message for settings
if [ $sys_admin_configured -eq 0 ]; then
    printf "\n  [FALSE] The system administrator account has been configured"
elif [ $sys_admin_configured -eq 1 ]; then
    printf "\n  [TRUE]  The system administrator account  has been configured"
else
    printf "\n  [N/A]   The system administrator account  has been configured"
fi
# status message for database settings
if [ $database_settings_configured -eq 0 ]; then
    printf "\n  [FALSE] The database settings have been configured"
elif [ $database_settings_configured -eq 1 ]; then
    printf "\n  [TRUE]  The database settings have been configured"
else
    printf "\n  [N/A]  The database settings have been configured"
fi
# status message for database creation
if [ $database_created -eq 0 ]; then
    printf "\n  [FALSE] The database has been created"
elif [ $database_created -eq 1 ]; then
    printf "\n  [TRUE]  The database has been created"
else
    printf "\n  [N/A]  The database has been created"
fi

printf "\n"

# if the settings have not been configured, configure them
if [ $settings_configured -eq 0 ]; then

    printf "\n" && printf "%60s" " " | tr ' ' '#' && printf "\n"
    printf "The main settings have not been configured, this will be done now.\n\n"

    # staff username config
    printf "%60s" " " | tr ' ' '-' && printf "\n"
    printf "Staff username settings\n-\n"
    if $(yes_no_query "Would you like to configure the staff username settings"); then
        printf "\n"
        # min/max username length
        standard_input_query "Minimum username length [leave empty for default] (4)" "username_settings" "username_min_length" 4 "n"
        standard_input_query "Maximum username length [leave empty for default, use -1 for no limit] (20)" "username_settings" "username_max_length" 20 "n"

        # capitals in usernames
        if $(yes_no_query "Allow capitals in usernames"); then
            edit_ini_line "username_settings" "username_accepts_capitals" "'yes'"
        else
            edit_ini_line "username_settings" "username_accepts_capitals" "'no'" 
        fi

        # numbers in usernames
        if $(yes_no_query "Allow numbers in usernames"); then
            edit_ini_line "username_settings" "username_accepts_numbers" "'yes'"
        else
            edit_ini_line "username_settings" "username_accepts_numbers" "'no'"
        fi

        # symbols in usernames
        if $(yes_no_query "Allow symbols in usernames"); then
            edit_ini_line "username_settings" "username_accepts_symbols" "'yes'"
        else
            edit_ini_line "username_settings" "username_accepts_symbols" "'no'"
        fi

    fi

    # staff password complexity config
    printf "%60s" " " | tr ' ' '-' && printf "\n"
    printf "Staff password complexity settings\n-\n"
    if $(yes_no_query "Would you like to configure the staff password complexity settings"); then
        printf "\n"
        # salt length
        standard_input_query "Input the password salt length [leave empty for default] (16)" "password_settings" "password_salt_length" 16 "n"
        # min/max length
        standard_input_query "Minimum password length or [leave empty for default] (8)" "password_settings" "password_min_length" 8 "n"
        standard_input_query "Maximum password or [leave empty for default, use -1 for no limit] (32)" "password_settings" "password_max_length" 32 "n"
        # min/max capitals
        standard_input_query "Minimum amount of capitals or [leave empty for default] (1)" "password_settings" "password_min_capitals" 1 "n"
        standard_input_query "Maximum amount of capitals or [leave empty for default, use -1 for no limit] (no limit)" "password_settings" "password_max_capitals" -1 "n"
        # min/max numbers
        standard_input_query "Minimum amount of numbers or [leave empty for default (1)" "password_settings" "password_min_numbers" 1 "n"
        standard_input_query "Maximum amount of numbers or [leave empty for default, use -1 for no limit] (no limit)" "password_settings" "password_max_numbers" -1 "n"
        # min/max symbols
        standard_input_query "Minimum amount of symbols or [leave empty for default] (0)" "password_settings" "password_min_symbols" 0 "n"
        standard_input_query "Maximum amount of symbols or [leave empty for default, use -1 for no limit] value (no limit)" "password_settings" "password_max_symbols" -1 "n"
    fi

    # regular user name settings
    printf "%60s" " " | tr ' ' '-' && printf "\n"
    printf "User name settings\n-\n"
    if $(yes_no_query "Would you like to configure the regular user name settings"); then
        printf "\n"

        # min/max username length
        standard_input_query "Minimum name length or [leave empty for default] (4)" "name_settings" "name_min_length" 3 "n"
        standard_input_query "Maximum name length or [leave empty for default, use -1 for no limit] (20)" "name_settings" "name_max_length" 30 "n"

        # numbers in names
        if $(yes_no_query "Allow numbers in names"); then
            edit_ini_line "name_settings" "name_accepts_numbers" "'yes'"
        else
            edit_ini_line "name_settings" "name_accepts_numbers" "'no'"
        fi
    fi

    # preferences
    printf "%60s" " " | tr ' ' '-' && printf "\n"
    printf "Preferences\n-\n"
    standard_input_query "When users are signed in, input the name of the location that should be displayed. This is case sensitive [leave empty for default] (Inside)" "preferences" "default_in_location" "Inside" "s"
    standard_input_query "When a new staff user account is created, they are given a default password if one is not provided. Please provide this default password. This is case sensitive [leave empty for default] (password)" "password_settings" "password_default" "password" "s"

    # toggles 'setup_settings' to true
    edit_ini_line "setup_settings" "settings_configured" "1"
    setup_settings=1
    sleep 0.5 # alows the ini file to be updated before continuing

fi

# System administrator settings configuration
if [ $sys_admin_configured -eq 0 ]; then

    printf "\n" && printf "%60s" " " | tr ' ' '#' && printf "\n"
    printf "The system administrator settings have not been configured, this will be done now.\n\n"

    # system administrator account settings
    printf "%60s" " " | tr ' ' '-' && printf "\n"
    printf "System administrator settings\n-\n"
    printf "A system administrator is required, this user has total control over the application when using the web interface.\n\n"
    standard_input_query "Input a name for this user [leave empty for default] (sysadmin)" "system_administrator_credentials" "sys_username" "sysadmin" "s"
    standard_input_query "Input a password for this user" "system_administrator_credentials" "sys_password" "" "s"
    printf "\n-\nYou can disable the system administrator user by changing the following line in app.ini: \n\nsys_enabled = 1\nTO\nsys_enabled = 0\n\n"

    if $(yes_no_query "Do you understand"); then
        printf "\nGood.\n"
    else
        printf "\nPlease read the README file for more information.\n"
    fi

    # toggles 'sys_admin_configured' to true
    edit_ini_line "setup_settings" "sys_admin_configured" "1"
    sys_admin_configured=1
    sleep 0.5 # alows the ini file to be updated before continuing

fi

# Database settings configuration
if [ $database_settings_configured -eq 0 ]; then

    printf "\n" && printf "%60s" " " | tr ' ' '#' && printf "\n"
    printf "The database settings have not been configured, this will be done now.\n\n"
    printf "%60s" " " | tr ' ' '-' && printf "\n"

    printf "Database settings\n-\n"
    standard_input_query "The application uses an SQL database, input a name for this database [leave empty for default] (dregDB)" "database" "db_name" "dregDB" "s"
    standard_input_query "The application also requires a user login to access the database, enter a username for it to use [leave empty for default] (dreg_user)" "database" "db_user" "dreg_user" "s"
    standard_input_query "This user requires a password, please enter an appropriate password" "database" "db_password" "" "s"

    # toggles 'database_settings_configured' to true
    edit_ini_line "setup_settings" "database_settings_configured" "1"
    database_settings_configured=1
    sleep 0.5 # alows the ini file to be updated before continuing

fi

# Database creation and setup
if [ $database_created -eq 0 ] && [ $database_settings_configured -eq 1 ] ; then

    printf "\n" && printf "%60s" " " | tr ' ' '#' && printf "\n"
    printf "The database has not been created, this will be done now.\n\n"
    printf "%60s" " " | tr ' the system administrator settings to having been configured' '-' && printf "\n"

    # check if MySQL is installed
    printf "\nChecking if MySQL is installed...\n"
    if [ -f /etc/init.d/mysql* ]; then
        printf "Success. MySQL seems to be installed.\n"
    else 
        if $(yes_no_query "MySQL could not be found on the machine, would you like to continue"); then
            continue
        else
            printf "\nAborted.\n"
            exit 1
        fi
    fi
    # fetch mySQL details
    printf "\nFetching database configuration details from app.ini...\n"
    db_hostname=$(read_ini_file "database" "db_hostname")
    db_name=$(read_ini_file "database" "db_name")
    db_user=$(read_ini_file "database" "db_user")
    db_password=$(read_ini_file "database" "db_password")
    printf "Done.\n"

    # create the database
    printf "\nCreating database...\n"
    if mysql -e "CREATE DATABASE IF NOT EXISTS $db_name;"; then
        printf "Success.\n";
    else
        exit 1;
    fi

    # change default charset
    printf "\nSetting default character set\n"
    if mysql -e "ALTER DATABASE $db_name DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;"; then
        printf "Success.\n";
    else
        exit 1;
    fi

    # add database user
    printf "\nCreating database user\n"
    if mysql -e "CREATE USER IF NOT EXISTS '$db_user'@'$db_hostname' IDENTIFIED BY '$db_password';"; then
        printf "Success.\n";
    else
        exit 1;
    fi

    # grant privileges to the user
    printf "\nGranting privileges to user\n"
    if mysql -e "GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'$db_hostname';"; then
        printf "Success.\n";
    else
        exit 1;
    fi

    # import SQL dump into the database
    printf "\nImporting SQL dump into the database\n"
    if mysql $db_name < dregDB.sql; then
        printf "Success.\n";
    else
        exit 1;
    fi

    # remove SQL dump as it is no longer needed
    printf "\nRemoving SQL dump as it is no longer needed\n"
    if rm dregDB.sql; then
        printf "Success.\n";
    else
        exit 1;
    fi

    # toggles 'database_created' to true
    edit_ini_line "setup_settings" "database_created" "1"
    database_created=1
    sleep 0.5 # alows the ini file to be updated before continuing
    printf "\nDone.\n"

fi

printf "\n" && printf "%60s" " " | tr ' ' '#' && printf "\n"

printf "\nThe settings have been configured successfully"
printf "\n" && printf "%60s" " " | tr ' ' '-' && printf "\n"

printf "\nIMPORTANT:\n"
printf "\n    - To change the settings, edit 'app.ini' with a text editor and change any variables\n"
printf "\n    - Changing the database settings will not change the database, but how the application accesses it"
printf "\n    - To change the database and any data, use SQL and update the details in app.ini"
printf "\n    - Changing the username settings will only affect new accounts or changes made to existing accounts."
printf "\n    - Changing the password complexity settings will only affect new passwords or newly changed passwords."
printf "\n\n    - To disable the system administrator account, set 'sys_enabled' to 0."
printf "\n    - Note that this will prevent the system administrator login from working in the web interface.\n"

printf "\nNEXT:\n"
printf "\n    - Sign into the web interface to continue configuring the application.\n"
printf "\n    - Enter http://localhost/doyrms-registration to access the application on THIS machine."
printf "\n    - OR, enter http://SERVER-IP/doyrms-registration on another machine.\n"

sys_username=$(read_ini_file "system_administrator_credentials" "sys_username")
sys_password=$(read_ini_file "system_administrator_credentials" "sys_password")
printf "\n    Username: $sys_username\n    Password: $sys_password\n"

printf "\n" && printf "%60s" " " | tr ' ' '#' && printf "\n"

exit 0