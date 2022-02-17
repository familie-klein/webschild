<?php

// #####################################################
// ################### Database ########################
// ############### by Michael Delissen #################
// ################ micdel.square7.de ##################
// #####################################################


class DatabaseException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
};


class Database
{
    // pdo object
    private $_pdo = null;

    public function __construct()
    {
        // nothing to do here
    }

    // prepares a statement
    public function prepare($statement)
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        return $this->_pdo->prepare($statement);
    }

    // bind a value to a statement
    public function bindValue(&$statement, $parameter, $value)
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        return $statement->bindValue($parameter, $value);

    }

    // bind an array of values to a statement
    public function bindValues(&$statement, $parameters)
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        if(is_array($parameters)) {

            foreach($parameters as $parameter => $value) {

                if(!$this->bindValue($statement, $parameter, $value)) {

                    throw new DatabaseException('Could not bind value: '.$parameter.'='.$value);

                }

            }

        } else {
            throw new DatabaseException('Parameters needs to be an array!');
        }

        return true;

    }

    // execute a statement
    public function execute(&$statement)
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        return $statement->execute();

    }

    // returns the result as associative array
    public function fetchAssoc(&$statement)
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        return $statement->fetch(PDO::FETCH_ASSOC);

    }

    // returns the result as object
    public function fetchObject(&$statement)
    {

        if(!$this->isConnected()) {
    
            throw new DatabaseException('Not connected to database.');

        }

        return $statement->fetch(PDO::FETCH_OBJ);

    }

    // executes a statement and binds parameters
    public function query($statement, $parameters = array())
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        $statement = $this->prepare($statement);

        if(!$this->bindValues($statement, $parameters)) {
            throw new DatabaseException('Could not bind values.');
        }

        $statement->execute();

        return $statement;
    }

    // check if statement was successfully executed
    public function error($statement)
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        return $statement === false || $statement->errorCode() != "00000";

    }

    // closes a statement
    public function closeStatement(&$statement)
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        return $statement->closeCursor();

    }

    // get the last insert id
    public function lastInsertId()
    {

        if(!$this->isConnected()) {

            throw new DatabaseException('Not connected to database.');

        }

        return $this->_pdo->lastInsertId();

    }

    // connect to database
    public function connect($dsn, $user, $name)
    {

        try {

            $this->_pdo = new PDO($dsn, $user, $name);

        } catch(PDOException $e) {

            $this->_pdo = null;

            throw new DatabaseException('Database connection failed.');

        }

    }

    // disconnect
    public function disconnect()
    {

        unset($this->_pdo);
        $this->_pdo = null;

    }

    // check if connected
    public function isConnected()
    {

        return $this->_pdo != null;

    }

};

// ############### Database ############################
?>
