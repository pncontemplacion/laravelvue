<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
         <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users </h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal">Add New
                        <i class="fas fa-user-plus fa-fw"></i>
                    </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Registered at</th>
                        <th>Modify</th>
                      </tr>
                  <tr v-for="user in users.data" :key="user.id">
                    <td>{{user.id}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.type | upText }}</td>
                    <td>{{user.created_at | myDate }}</td>
                    <td>
                        <a href="#" @click="editModal(user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                            /
                         <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                    </td>
                  </tr>

                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users"
                    @pagination-change-page="getResults">
                  </pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>

                <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 v-show="!editmode" class="modal-title" id="addNewLabel">Add New</h5>
                <h5 v-show="editmode" class="modal-title" id="addNewLabel">Update User's Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form @submit.prevent="editmode ? updateUser(): createUser()">
            <div class="modal-body">

                <div class="form-group">
                    <input v-model="form.name" type="text" name="name"
                        placeholder="Enter name here"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                    <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                    <input v-model="form.email" type="email" name="email"
                        placeholder="Enter a valid e-mail address"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                    <has-error :form="form" field="email"></has-error>
                </div>

                <div class="form-group">
                    <textarea v-model="form.bio" name="bio" id="bio"
                        placeholder="Short bio for user (Optional)"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                    </textarea>
                    <has-error :form="form" field="bio"></has-error>
                </div>

                <div class="form-group">
                    <select v-model="form.type" name="type" id="type"
                       class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                            <option value="">Select User Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">Regular User</option>
                            <option value="author">Author</option>
                    </select>
                    <has-error :form="form" field="type"></has-error>
                </div>

                  <div class="form-group">
                    Password
                    <input v-model="form.password" type="password" name="password" id="password"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                    <has-error :form="form" field="password"></has-error>
                </div>

            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
            </div>
            </form>



            </div>
        </div>
        </div>

    </div>




</template>

<script>
    export default {
        data() {

            return {
                editmode: false,
                users: {},
                form: new Form({
                    id:'',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('api/users?page=' + page)
                  .then(response => {
                      this.users = response.data;
                  });
            },
            updateUser() {
                this.$Progress.start();
                this.form.put('api/user/' + this.form.id)
                .then(() => {
                     $('#addNew').modal('hide');
                     Swal.fire(
                             'Updated!',
                             'Information has been updated.',
                             'success'
                    )

                    this.$Progress.finish();
                    Fire.$emit('AfterAction');

                })
                .catch(()=>{
                    this.$Progress.fail();
                });
            },
            newModal() {
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            editModal(user) {
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            deleteUser(id) {
                    Swal.fire({
                        title: 'Are you sure?',
                         text: "You won't be able to revert this!",
                         type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                    //Send Ajax request to the server
                    if (result.value) {
                         this.form.delete('api/user/' + id).then(()=>{
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                 )
                                Fire.$emit('AfterAction');
                    }).catch(() => {
                        Swal.fire("Failed!", "There was something wrong.", "warning");
                    });
                  }
                })
            },  //end deleteUser()
            loadUsers() {
               if(this.$gate.isAdmin() || this.$gate.isAuthor()){
                    axios.get("api/user").then(({ data }) => (this.users = data));
                }
            },
            createUser() {

                this.$Progress.start();
                this.form.post('api/user')
                .then(()=>{
                    $('#addNew').modal('hide')

                    Toast.fire({
                         type: 'success',
                        title: 'User created successfully successfully'
                    })

                    Fire.$emit('AfterAction');
                    this.$Progress.finish();
                })
                .catch(() => this.$Progress.fail())
            }
        },
        created() {
            Fire.$on('searching', () => {
                let query = this.$parent.search;
                axios.get('api/findUser?q=' + query)
                .then((data) => {
                    this.users = data.data;
                })
                .catch(() => {

                })
            });
            this.loadUsers();
            Fire.$on('AfterAction', () => {
                this.loadUsers();
            });
        }
    }
</script>
