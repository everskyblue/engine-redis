%{extends "layout.layout"}

%{block "content"}

  <p>
    {! $name . " edad: " . $obj->age . " pais :" . $obj->country !}
  </p>

%{endblock}
