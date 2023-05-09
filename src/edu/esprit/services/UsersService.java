/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package edu.esprit.services;

import edu.esprit.util.DataSource;
import java.sql.Connection;
import edu.esprit.entities.users;
import static java.sql.JDBCType.NULL;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;

/**
 *
 * @author wassim
 */
public class UsersService {
            Connection cnx = DataSource.getInstance().getCnx();
        public String n, m;
        public String passwordF;
        
        //Fonction d'ajout d'un utilisateur
   public void ajouter(users u) throws SQLException {
    String req = "INSERT INTO `users`(  `nom`, `prenom`, `email`, `password`,`adresse`, `telephone`, `code_postale`)  VALUES (?,?,?,?,?,?,?)";
    PreparedStatement ps = cnx.prepareStatement(req);
    try {
        
       
        
        ps.setString(3, u.getNom());
        ps.setString(4, u.getPrenom());
        ps.setString(1, u.getEmail());
        ps.setString(2, u.getPassword());
        ps.setString(5, u.getAdresse());
        ps.setInt(6, u.getTelephone());
        ps.setInt(7, u.getCode_postale());

        ps.executeUpdate();
        System.out.println("Utilisateur created !");
    } catch (SQLException ex) {
        System.out.println(ex.getMessage());
    }
}


public void modifier(int id, users u) {
    String sql = "UPDATE users SET  nom=?, prenom=?, email=?, password=?, adresse=?, telephone=?, code_postale=? WHERE id=" + id;
    try {
        PreparedStatement ste = cnx.prepareStatement(sql);
        
        ste.setString(3, u.getNom());
        ste.setString(4, u.getPrenom());
        ste.setString(1, u.getEmail());
        ste.setString(2, u.getPassword());
        ste.setString(5, u.getAdresse());
        ste.setInt(6, u.getTelephone());
        ste.setInt(7, u.getCode_postale());

        ste.executeUpdate();
        System.out.println("********************** MODIFIED ****************************************");
    } catch (SQLException ex) {
        System.out.println(ex.getMessage());
    }
}

        
                //Fonction d'affichage de tous les utilisateurs
    public List<users> getAll() {
    List<users> list = new ArrayList<>();
    try {
        String req = "Select * from users";
        Statement st = cnx.createStatement();
        ResultSet rs = st.executeQuery(req);
        while (rs.next()) {
            users u = new users(
                rs.getInt("id"), 
                rs.getString("nom"),
                rs.getString("prenom"),
                rs.getString("email"),
                rs.getString("password"),
                rs.getString("adresse"),
                rs.getInt("telephone"),
                rs.getInt("code_postale")
            );
            list.add(u);
        }
    } catch (SQLException ex) {
        System.out.println(ex.getMessage());
    }

    return list;
}


    

    
     //Fonction de supression d'un utilisateur
    public void delete(int id) {
        try {
            String req = "DELETE FROM `users` WHERE id  = " + id;
            Statement st = cnx.createStatement();
            st.executeUpdate(req);
            System.out.println("Utilisateur deleted !");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }
    }



}