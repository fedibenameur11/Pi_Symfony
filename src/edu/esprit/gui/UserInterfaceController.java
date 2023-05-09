/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package edu.esprit.gui;
import edu.esprit.entities.users;
import edu.esprit.services.UsersService;
import java.io.IOException;
import javafx.fxml.Initializable;
import javafx.scene.control.Button;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.scene.control.cell.PropertyValueFactory;

import java.net.URL;

import java.util.ResourceBundle;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.ButtonType;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.stage.StageStyle;

public class UserInterfaceController implements Initializable {

    @FXML
    private Button addUserButton;

    @FXML
    private Button editUserButton;

    @FXML
    private Button deleteUserButton;

    @FXML
    private Button refreshButton;

    @FXML
    private TableView<users> usersTable;

    @FXML
    private TableColumn<users, Integer> idColumn;

    @FXML
    private TableColumn<users, String> emailColumn;

    @FXML
    private TableColumn<users, String> passwordColumn;

    @FXML
    private TableColumn<users, String> nomColumn;

    @FXML
    private TableColumn<users, String> prenomColumn;

    @FXML
    private TableColumn<users, String> adresseColumn;

    @FXML
    private TableColumn<users, Integer> telephoneColumn;

    @FXML
    private TableColumn<users, Integer> codePostaleColumn;

    private UsersService usersService;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        usersService = new UsersService();

    idColumn.setCellValueFactory(new PropertyValueFactory<>("id"));
    emailColumn.setCellValueFactory(new PropertyValueFactory<>("email"));
    passwordColumn.setCellValueFactory(new PropertyValueFactory<>("password"));
    nomColumn.setCellValueFactory(new PropertyValueFactory<>("nom"));
    prenomColumn.setCellValueFactory(new PropertyValueFactory<>("prenom"));
    adresseColumn.setCellValueFactory(new PropertyValueFactory<>("adresse"));
    telephoneColumn.setCellValueFactory(new PropertyValueFactory<>("telephone"));
    codePostaleColumn.setCellValueFactory(new PropertyValueFactory<>("code_postale"));

        loadUsers();

        addUserButton.setOnAction(e -> {
    try {
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("AddUser.fxml"));
        Parent parent = fxmlLoader.load();
        Stage stage = new Stage();
        stage.initModality(Modality.APPLICATION_MODAL);
        stage.initStyle(StageStyle.DECORATED);
        stage.setTitle("Add User");
        stage.setScene(new Scene(parent));
        stage.showAndWait();
        loadUsers();
    } catch (IOException ex) {
        System.out.println("Error opening Add User dialog: " + ex.getMessage());
    }
});


  editUserButton.setOnAction(e -> {
    users selectedUser = usersTable.getSelectionModel().getSelectedItem();
    if (selectedUser != null) {
        try {
            FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("ModifyUser.fxml"));
            Parent parent = fxmlLoader.load();

            ModifyUserController modifyUserController = fxmlLoader.getController();
            modifyUserController.setCurrentUser(selectedUser);

            Stage stage = new Stage();
            stage.initModality(Modality.APPLICATION_MODAL);
            stage.initStyle(StageStyle.DECORATED);
            stage.setTitle("Modify User");
            stage.setScene(new Scene(parent));
            stage.showAndWait();
            loadUsers();
        } catch (IOException ex) {
            System.out.println("Error opening Modify User dialog: " + ex.getMessage());
        }
    } else {
        System.out.println("No user selected for editing.");
    }
});






        deleteUserButton.setOnAction(e -> {
    // Delete the selected user
    users selectedUser = usersTable.getSelectionModel().getSelectedItem();
    if (selectedUser != null) {
        usersService.delete(selectedUser.getId());
        loadUsers();
    }
});




        refreshButton.setOnAction(e -> loadUsers());
    }
    

    private void loadUsers() {
        try {
            ObservableList<users> userList = FXCollections.observableArrayList(usersService.getAll());
            usersTable.setItems(userList);
        } catch (Exception e) {
            System.out.println(e.getMessage());
        }
    }
    

}
