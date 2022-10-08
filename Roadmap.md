
# SteelProject  Roadmap  

![11%](https://progress-bar.dev/11/?width=500&title=Project%20Competion)

##  0.0.2.0   ![54%](https://progress-bar.dev/54/?width=250&title=Progress) 

- [ ] Users Management   ![66%](https://progress-bar.dev/66/?width=250&title=Progress) 

	- [x] Create migrations for users

	- [x] Basic Users' implementation

	- [ ] Sign in/up   

- [ ] User Roles ![75%](https://progress-bar.dev/75/?width=250&title=Progress) 

	- [x] Create roles' migrations

	- [x] Create permissions' migrations

	- [x] Basic Site Roles

	- [ ] Middle-wares for protection  

- [ ] Translation  

	- [ ] Create Database translation migrations  

	- [ ] Make translations context specific   

	- [ ] Create an extractor to extract translatable strings from view and classes  

- [ ] Console ![75%](https://progress-bar.dev/75/?width=250&title=Progress) 

	- [x] Update console script to support colors  

	- [x] Update console script to handle sorted migrations/seeders 

	- [x] Separate the console functional logic from display logic 

	- [ ] Add a progress bar to console tasks  

##  0.0.3.0 ![25%](https://progress-bar.dev/25/?width=250&title=Progress) 

- [ ] Organisations   

	- [ ] Relate Organisations to specific users who'll have their administration role   

	- [ ] Protect organisations   

- [ ] Organisation Roles ![50%](https://progress-bar.dev/50/?width=250&title=Progress)

	- [ ] Access levels are defined by roles  (RBAC)  

	- [x] Add permissions to existing roles system  

##  0.0.4.0   

- [ ] Teams   

	- [ ] An organisation is a set of teams   

	- [ ] Teams have managers   

	- [ ] Organisation roles are inherited within teams   

	- [ ] Team members with sufficient ability may invite others to their team   

##  0.0.5.0   

- [ ] Chat System   

	- [ ] Implement organisation wide chat system   

	- [ ] Implement a chat system for teams  

##  0.1.0.0   

- [ ] Projects system   

	- [ ] Organisations can have projects   

	- [ ] Projects are assigned to teams   

- [ ] Tickets system   

	- [ ] Projects are divided into stories represented by tickets   

	- [ ] Tickets might be assigned to at team members   

	- [ ] Tickets have some required properties  

## 0.1.1.0  

- [ ] Time Tracking  

	- [ ] Add feature for users to track time spent on cards  

	- [ ] Grant higher roles in projects/organisations the ability to review and confirm time tracks  

## 1.0.0.0  

- [ ] Github integration   

	- [ ] Support linking github repos to projects   

	- [ ] Suppot linking tasks to branches   

- [ ] Git flow integration   

	- [ ] Support Git flow implementation on tasks/branches   

	- [ ] Add option to project init/settings (if possible)   
