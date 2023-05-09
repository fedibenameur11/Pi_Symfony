package edu.esprit.gui;

import edu.esprit.entities.users;
import edu.esprit.services.UsersService;
import java.net.URL;
import java.util.ResourceBundle;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Button;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

public class ModifyUserController implements Initializable {

    @FXML
    private TextField emailField;
    @FXML
    private TextField passwordField;
    @FXML
    private TextField nomField;
    @FXML
    private TextField prenomField;
    @FXML
    private TextField adresseField;
    @FXML
    private TextField telephoneField;
    @FXML
    private TextField codePostaleField;
    @FXML
    private Button modifyButton;

    private UsersService usersService;
    private users currentUser;

    public void setCurrentUser(users user) {
        currentUser = user;

        emailField.setText(user.getEmail());
        passwordField.setText(user.getPassword());
        nomField.setText(user.getNom());
        prenomField.setText(user.getPrenom());
        adresseField.setText(user.getAdresse());
        telephoneField.setText(String.valueOf(user.getTelephone()));
        codePostaleField.setText(String.valueOf(user.getCode_postale()));
    }

    @Override
    public void initialize(URL url, ResourceBundle rb) {
        usersService = new UsersService();

        modifyButton.setOnAction(e -> {
            users updatedUser = new users(
                   
            currentUser.getId(),
            emailField.getText(),
            passwordField.getText(),
            nomField.getText(),
            prenomField.getText(),
            adresseField.getText(),
            Integer.parseInt(telephoneField.getText()),
            Integer.parseInt(codePostaleField.getText())
    );

    usersService.modifier(currentUser.getId(), updatedUser);

    // Close the dialog after modifying the user
    Stage stage = (Stage) modifyButton.getScene().getWindow();
    stage.close();
});
}
    

}
